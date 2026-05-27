let currentConversation = 0;
let isSending = false;

function getMessagesBox() {
    return document.getElementById("messages");
}

function appendMessage(type, text) {
    const box = getMessagesBox();
    const div = document.createElement("div");

    div.className = type === "user" ? "user-message" : "bot-message";
    div.innerText = text;

    box.appendChild(div);
    box.scrollTop = box.scrollHeight;

    return div;
}

function appendBotHTML(html) {
    const box = getMessagesBox();
    const div = document.createElement("div");

    div.className = "bot-message";
    div.innerHTML = html;

    box.appendChild(div);
    box.scrollTop = box.scrollHeight;

    return div;
}

function removeLastThinkingMessage() {
    const box = getMessagesBox();
    const messages = box.querySelectorAll(".bot-message");

    if (messages.length === 0) return;

    const last = messages[messages.length - 1];

    if (
        last.innerText.trim() === "Đang suy nghĩ..." ||
        last.innerText.trim() === "Đang suy nghĩ..."
    ) {
        last.remove();
    }
}

function saveMessage(convId, sender, content) {
    return fetch("../api/messages/save.php", {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
            `conversation_id=${encodeURIComponent(convId)}&sender=${encodeURIComponent(sender)}&content=${encodeURIComponent(content)}`
    })
    .then(res => res.text());
}

function updateConversationTitle(id, title) {
    const shortTitle = title.substring(0, 60);

    return fetch("../api/conversations/update_title.php", {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
            `id=${encodeURIComponent(id)}&title=${encodeURIComponent(shortTitle)}`
    })
    .then(res => res.text());
}

function ensureConversation(firstText) {
    if (currentConversation && parseInt(currentConversation) > 0) {
        return Promise.resolve(parseInt(currentConversation));
    }

    return fetch("../api/conversations/create.php", {
        credentials: "same-origin"
    })
    .then(res => res.text())
    .then(idText => {
        const id = parseInt(idText);

        if (!id || id <= 0) {
            throw new Error("Không tạo được cuộc trò chuyện mới. Kiểm tra session đăng nhập hoặc bảng conversations.");
        }

        currentConversation = id;

        return updateConversationTitle(id, firstText)
            .then(() => id);
    });
}

function send() {
    if (isSending) return;

    const input = document.getElementById("inputText");
    const text = input.value.trim();

    if (text === "") return;

    isSending = true;

    appendMessage("user", text);
    input.value = "";

    ensureConversation(text)
        .then(convId => {
            currentConversation = convId;

            return saveMessage(convId, "user", text)
                .then(() => {
                    loadConversations(false);

                    if (shouldAutoSuggestAI(text)) {
                        return suggestTopAIFromText(text);
                    }

                    return callRealtimeAI(text, convId);
                });
        })
        .catch(err => {
            appendMessage("bot", "Lỗi: " + err.message);
        })
        .finally(() => {
            isSending = false;
        });
}

function callRealtimeAI(text, convId) {
    return ensureConversation(text)
        .then(realConvId => {
            convId = convId && parseInt(convId) > 0 ? parseInt(convId) : realConvId;

            appendMessage("bot", "Đang suy nghĩ...");

            return fetch("../api/ai/realtime_chat.php", {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body:
                    `prompt=${encodeURIComponent(text)}&conversation_id=${encodeURIComponent(convId)}`
            });
        })
        .then(res => res.json())
        .then(data => {
            removeLastThinkingMessage();

            console.log("Ollama:", data);

            let reply = "";

            if (data.error) {
                reply = "Lỗi AI: " + data.error;
            } else if (data.response) {
                reply = data.response;
            } else {
                reply = "Không nhận được phản hồi từ AI. Hãy kiểm tra đã chạy chưa và model đã pull chưa.";
            }

            appendMessage("bot", reply);

            return saveMessage(convId, "bot", reply)
                .then(() => {
                    loadConversations(false);
                });
        })
        .catch(err => {
            removeLastThinkingMessage();

            const errorText = "Fetch lỗi: " + err.message;

            appendMessage("bot", errorText);

            if (convId && convId > 0) {
                return saveMessage(convId, "bot", errorText)
                    .then(() => loadConversations(false));
            }

            isSending = false;
        })
        .finally(() => {
            isSending = false;
        });
}

function newChat() {
    currentConversation = 0;

    const box = getMessagesBox();
    box.innerHTML = "";

    appendMessage(
        "bot",
        "Xin chào 👋\nHãy mô tả mục tiêu học tập hoặc công việc của bạn.\nVí dụ:\n- Tôi muốn dùng AI để học lập trình web\n- Tôi muốn dùng AI để làm báo cáo Word\n- Tôi muốn luyện prompt ChatGPT\n- Tôi muốn chọn AI phù hợp để học IELTS"
    );
}

function loadMessages(id) {
    fetch("../api/messages/get.php?id=" + encodeURIComponent(id), {
        credentials: "same-origin"
    })
    .then(res => res.json())
    .then(data => {
        const box = getMessagesBox();
        box.innerHTML = "";

        if (!data || data.length === 0) {
            appendMessage("bot", "Cuộc trò chuyện này chưa có tin nhắn.");
            return;
        }

        data.forEach(msg => {
            if (
                msg.sender === "bot" &&
                (
                    msg.content.includes("top-ai-box") ||
                    msg.content.includes("review-result-box")
                )
            ) {
                appendBotHTML(msg.content);
            } else {
                appendMessage(msg.sender, msg.content);
            }
        });
    })
    .catch(err => {
        appendMessage("bot", "Lỗi tải tin nhắn: " + err.message);
    });
}

function loadConversations(autoOpen = false) {
    fetch("../api/conversations/get.php", {
        credentials: "same-origin"
    })
    .then(res => res.json())
    .then(data => {
        const list = document.getElementById("historyList");
        list.innerHTML = "";

        data.forEach(conv => {
            const li = document.createElement("li");
            li.innerText = conv.title || "Đoạn chat mới";

            li.onclick = () => {
                currentConversation = parseInt(conv.id);
                loadMessages(conv.id);
            };

            list.appendChild(li);
        });

        if (autoOpen && data.length > 0) {
            currentConversation = parseInt(data[0].id);
            loadMessages(data[0].id);
        }
    })
    .catch(err => {
        appendMessage("bot", "Lỗi tải lịch sử: " + err.message);
    });
}

function suggestTopAI() {
    const input = document.getElementById("inputText");
    const text = input.value.trim();

    if (text === "") {
        appendMessage(
            "bot",
            "Bạn hãy nhập nhu cầu trước. Ví dụ: Tôi muốn học code, làm báo cáo Word, học IELTS, nghiên cứu tài liệu..."
        );
        return;
    }

    ensureConversation(text)
        .then(convId => {
            currentConversation = convId;
            return suggestTopAIFromText(text);
        });
}

function suggestTopAIFromText(text) {
    appendMessage("bot", "Đang suy nghĩ...");

    return fetch("../api/ai/ollama_suggest.php?q=" + encodeURIComponent(text), {
        credentials: "same-origin"
    })
    .then(res => res.json())
    .then(data => {
        removeLastThinkingMessage();

        if (data.error) {
            appendMessage("bot", "Lỗi gợi ý AI: " + data.error);
            return saveMessage(currentConversation, "bot", "Lỗi gợi ý AI: " + data.error);
        }

        if (!data.items || data.items.length === 0) {
            appendMessage("bot", "chưa tìm được AI phù hợp.");
            return saveMessage(currentConversation, "bot", "chưa tìm được AI phù hợp.");
        }

        let html = `
            <div class="top-ai-box">
                <h2>Đang suy nghĩ</h2>
                <p class="muted-text">Nhu cầu của bạn: ${escapeHTML(text)}</p>
        `;

        data.items.forEach((ai, index) => {
            html += `
                <div class="top-ai-card">
                    <div class="rank-badge">#${index + 1}</div>

                    <div class="top-ai-content">
                        <h3>${escapeHTML(ai.name)}</h3>

                        <p>${escapeHTML(ai.description || "Chưa có mô tả")}</p>

                        <p>
                            <b>Lý do chọn:</b>
                            ${escapeHTML(ai.reason || "Phù hợp với nhu cầu.")}
                        </p>

                        <div class="ai-meta">
                            <span>⭐ ${escapeHTML(ai.rating)}</span>
                            <span>Độ phù hợp: ${escapeHTML(ai.score)}/100</span>
                        </div>

                        <a href="${escapeHTML(ai.guide_url)}">
                            Mở trang hướng dẫn →
                        </a>
                    </div>
                </div>
            `;
        });

        html += `</div>`;

        appendBotHTML(html);

        return saveMessage(currentConversation, "bot", html)
            .then(() => {
                loadConversations(false);
            });
    })
    .catch(err => {
        removeLastThinkingMessage();

        const errorText = "Lỗi gọi Ollama: " + err.message;

        appendMessage("bot", errorText);

        return saveMessage(currentConversation, "bot", errorText);
    });
}

function shouldAutoSuggestAI(text) {
    const q = text.toLowerCase();

    const keywords = [
        "ai nào",
        "ai phù hợp",
        "gợi ý ai",
        "chọn ai",
        "dùng ai",
        "sử dụng ai",
        "học code",
        "học lập trình",
        "lập trình web",
        "làm word",
        "viết báo cáo",
        "làm báo cáo",
        "học ielts",
        "học hsk",
        "nghiên cứu tài liệu",
        "tóm tắt tài liệu",
        "prompt",
        "chatgpt",
        "claude",
        "gemini"
    ];

    return keywords.some(keyword => q.includes(keyword));
}

function reviewPrompt() {
    const input = document.getElementById("inputText");
    const prompt = input.value.trim();

    if (prompt === "") {
        appendMessage(
            "bot",
            "Bạn hãy nhập prompt cần đánh giá vào ô chat trước."
        );
        return;
    }

    fetch("../api/ai/prompt_review.php", {
        method: "POST",
        credentials: "same-origin",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "prompt=" + encodeURIComponent(prompt)
    })
    .then(res => res.json())
    .then(data => {
        let feedbackHTML = "";

        if (data.feedback && data.feedback.length > 0) {
            data.feedback.forEach(item => {
                feedbackHTML += `<li>${escapeHTML(item)}</li>`;
            });
        } else {
            feedbackHTML = `<li>Prompt khá ổn, có thể dùng được.</li>`;
        }

        const html = `
            <div class="review-result-box">
                <h2>✍️ Đánh giá prompt</h2>

                <div class="score-circle">
                    ${escapeHTML(data.score)}/10
                </div>

                <h3>Góp ý</h3>
                <ul>
                    ${feedbackHTML}
                </ul>

                <h3>Prompt gợi ý tốt hơn</h3>
                <div class="prompt-suggestion">
                    Hãy đóng vai chuyên gia. Mục tiêu của tôi là ... 
                    Hãy trả lời chi tiết, có ví dụ, từng bước và bài tập thực hành.
                </div>
            </div>
        `;

        let plainText = `Đánh giá prompt\nĐiểm: ${data.score}/10\n`;

        if (data.feedback && data.feedback.length > 0) {
            plainText += "Góp ý:\n";
            data.feedback.forEach(item => {
                plainText += "- " + item + "\n";
            });
        } else {
            plainText += "Prompt khá ổn.\n";
        }

        plainText += "\nPrompt gợi ý tốt hơn:\n";
        plainText += "Hãy đóng vai chuyên gia. Mục tiêu của tôi là ... Hãy trả lời chi tiết, có ví dụ, từng bước và bài tập thực hành.";

        return ensureConversation(prompt)
            .then(convId => {
                appendBotHTML(html);
                return saveMessage(convId, "bot", html);
            })
            .then(() => {
                loadConversations(false);
            });
    })
    .catch(err => {
        appendMessage("bot", "Lỗi đánh giá prompt: " + err.message);
    });
}

function showTrendingAI() {
    currentConversation = 0;

    const box = getMessagesBox();
    box.innerHTML = "";

    fetch("../api/ai/trending.php", {
        credentials: "same-origin"
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            appendMessage("bot", "Lỗi tải AI thịnh hành: " + data.error);
            return;
        }

        if (!data.items || data.items.length === 0) {
            appendMessage(
                "bot",
                "Chưa có dữ liệu AI thịnh hành. Hãy kiểm tra bảng ai_tools."
            );
            return;
        }

        let html = `
            <div class="trending-board">
                <h2>🔥 AI HOT NHẤT HIỆN NAY</h2>
                <p class="muted-text trending-subtitle">
                    Dựa trên độ phổ biến và tính ứng dụng thực tế
                </p>

                <div class="trending-table">
                    <div class="trending-row trending-head">
                        <div>Logo</div>
                        <div>Tên AI</div>
                        <div>Điểm nổi bật</div>
                        <div>Điểm</div>
                        <div>Hạng</div>
                    </div>
        `;

        data.items.forEach(ai => {
            const guideUrl = ai.guide_url || ("ai_workspace.php?id=" + ai.id);

            const logo = ai.logo_url
                ? `<img class="ai-logo" src="${escapeHTML(ai.logo_url)}" alt="${escapeHTML(ai.name)}">`
                : `<div class="ai-logo-fallback">AI</div>`;

            html += `
                <div class="trending-row trending-item"
                     onclick="window.location.href='${escapeHTML(guideUrl)}'">

                    <div class="logo-cell">
                        ${logo}
                    </div>

                    <div class="ai-name">
                        ${escapeHTML(ai.name)}
                    </div>

                    <div class="ai-feature">
                        ${escapeHTML(ai.trend_feature || ai.description || "Chưa có mô tả")}
                    </div>

                    <div class="score-cell">
                        <span class="trend-score">
                            ${escapeHTML(ai.trend_score || ai.rating || 0)}/10
                        </span>
                    </div>

                    <div class="rank-cell">
                        <span class="trend-rank">
                            ${escapeHTML(ai.trend_rank || "")}
                        </span>
                    </div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;

        appendBotHTML(html);
    })
    .catch(err => {
        appendMessage("bot", "Lỗi tải bảng AI thịnh hành: " + err.message);
    });
}

function escapeHTML(value) {
    if (value === null || value === undefined) return "";

    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

document.getElementById("sendBtn").addEventListener("click", send);

document.getElementById("inputText").addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
        send();
    }
});

loadConversations(false);
newChat();