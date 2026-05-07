let currentConvId = null;

const sendBtn = document.getElementById('sendBtn');
const inputText = document.getElementById('inputText');
const messagesDiv = document.getElementById('messages');
const historyList = document.getElementById('historyList');

// 1. HÀM GỬI TIN NHẮN (Đã sửa lỗi đồng nhất Database)
async function sendMessage() {
    const text = inputText.value.trim();
    if (!text) return;

    // Nếu là tin nhắn đầu tiên của cuộc hội thoại mới
    if (!currentConvId) {
        // 1. Tạo cuộc hội thoại với tiêu đề chính là nội dung tin nhắn
        // const res = await fetch('../api/conversations/create.php?title=' + encodeURIComponent(text));
        const res = await fetch(`../api/conversations/create.php?title=${encodeURIComponent(text)}`);
        currentConvId = await res.text();
    }

    appendMessage('user', text);
    inputText.value = '';

    try {
        // 2. Lưu tin nhắn User vào database
        await fetch('../api/messages/save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `conv_id=${currentConvId}&sender=user&content=${encodeURIComponent(text)}`
        });

        // 3. Gọi AI gợi ý
        const response = await fetch(`../api/ai/suggest.php?q=${encodeURIComponent(text)}`);
        const data = await response.json();

        let aiReplyHTML = "";
        if (data && data.length > 0) {
            const top5 = data.slice(0, 5);
            aiReplyHTML = `<div class="suggestion-box"><p class="suggestion-title">Top 5 AI phù hợp:</p><div class="ai-grid">`;
            top5.forEach(ai => {
                aiReplyHTML += `
                    <div class="ai-card" onclick="goToPractice('${ai.name}')">
                        <div class="ai-info"><span class="ai-name">${ai.name}</span><span class="ai-score">${ai.score}/10 ⭐</span></div>
                        <p class="ai-hint">Nhấn để luyện tập cùng ${ai.name}</p>
                    </div>`;
            });
            aiReplyHTML += `</div></div>`;
        } else {
            aiReplyHTML = "Không tìm thấy AI nào phù hợp.";
        }

        // 4. Hiển thị và LƯU TOÀN BỘ HTML vào Database (Để khi load lại vẫn còn Top 5)
        appendMessage('bot', aiReplyHTML);

        await fetch('../api/messages/save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `conv_id=${currentConvId}&sender=bot&content=${encodeURIComponent(aiReplyHTML)}`
        });

        loadHistory(); // Cập nhật lại danh sách bên trái để hiện Title mới
    } catch (error) {
        appendMessage('bot', "Lỗi kết nối.");
    }
}

// Sang trang Thực hành
function goToPractice(aiName) {
    // Chuyển hướng sang thư mục practice_ai kèm theo AI đã chọn
    window.location.href = `../practice_ai/index.php?ai=${encodeURIComponent(aiName)}`;
}
// 2. HÀM LỊCH SỬ (Hiển thị sử sách từ Database)
async function loadHistory() {
    try {
        const res = await fetch('../api/conversations/get.php');
        const list = await res.json();

        historyList.innerHTML = '';

        if (!list || list.length === 0) {
            historyList.innerHTML = '<li style="font-style: italic; opacity: 0.5;">Chưa có lịch sử...</li>';
            return;
        }

        list.forEach(item => {
            const li = document.createElement('li');
            li.innerHTML = `💬 ${item.title || "Cuộc trò chuyện mới"}`;
            li.onclick = () => loadDetailChat(item.id);
            historyList.appendChild(li);
        });
    } catch (e) {
        console.log("Lỗi tải lịch sử.");
    }
}

// 3. TẢI LẠI ĐOẠN CHAT CŨ
async function loadDetailChat(id) {
    currentConvId = id;
    messagesDiv.innerHTML = '';
    try {
        const res = await fetch(`../api/messages/get.php?id=${id}`);
        const msgs = await res.json();
        msgs.forEach(m => appendMessage(m.sender, m.content));
    } catch (err) {
        console.error("Không thể khôi phục đoạn chat:", err);
    }
}

// 4. HÀM HIỂN THỊ TIN NHẮN 
function appendMessage(sender, content) {
    const row = document.createElement('div');
    row.className = `message-row ${sender === 'user' ? 'user-row' : ''}`;

    // Sử dụng innerHTML để các thẻ <div class="ai-card"> được render đúng
    row.innerHTML = `
        ${sender === 'bot' ? '<div class="avatar">🤖</div>' : ''}
        <div class="bubble ${sender === 'user' ? 'user-bubble' : 'bot-bubble'}">
            ${content} 
        </div>
        ${sender === 'user' ? '<div class="avatar">👤</div>' : ''}
    `;

    messagesDiv.appendChild(row);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

// 5. ĐIỀU HƯỚNG & KHỞI CHẠY
function newChat() {
    currentConvId = null;
    messagesDiv.innerHTML = `
        <div class="message-row">
            <div class="avatar">🤖</div>
            <div class="bubble bot-bubble">Rất vui được gặp bạn, tôi có thể giúp gì cho bạn hôm nay?</div>
        </div>`;
}

sendBtn.onclick = sendMessage;
inputText.addEventListener('keypress', (e) => { if (e.key === 'Enter') sendMessage(); });

window.goLogin = () => window.location.href = '../Login/dangnhap.php';
window.goRegister = () => window.location.href = '../Login/dangky.php';
window.newChat = newChat;
window.onload = loadHistory;

// Tự động load lịch sử khi trang vừa tải xong
window.addEventListener('DOMContentLoaded', (event) => {
    loadHistory();
});