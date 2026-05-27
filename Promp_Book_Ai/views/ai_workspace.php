<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include __DIR__ . "/../config/connect.php";

$id = intval($_GET['id'] ?? 0);

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM ai_tools WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$ai = mysqli_fetch_assoc($result);

if (!$ai) {
    echo "Không tìm thấy AI";
    exit;
}

$useCases = array_filter(array_map('trim', explode("\n", $ai['use_cases'] ?? "")));
$exercises = array_filter(array_map('trim', explode("\n", $ai['exercises'] ?? "")));
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($ai['name']); ?> - AI Workspace</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            margin: 0;
            background: #0f172a;
            color: white;
            font-family: Arial;
            height: 100vh;
            overflow: hidden;
            display: block;
        }

        .workspace {
            display: grid;
            grid-template-columns: 320px 1fr 320px;
            height: 100vh;
        }

        .left-panel,
        .right-panel {
            background: #111827;
            border-right: 1px solid #334155;
            padding: 24px;
            overflow: auto;
        }

        .right-panel {
            border-right: none;
            border-left: 1px solid #334155;
        }

        .center-panel {
            display: flex;
            flex-direction: column;
            padding: 24px;
            overflow: hidden;
        }

        .search-box {
            background: #020617;
            border: 1px solid #1e293b;
            padding: 12px;
            border-radius: 12px;
            color: white;
            width: 100%;
            margin-bottom: 25px;
        }

        .big-title {
            font-size: 32px;
            line-height: 1.3;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 24px;
            margin-top: 30px;
        }

        .guide-box,
        .prompt-box,
        .exercise-box {
            background: #020617;
            border: 1px solid #1e293b;
            border-radius: 14px;
            padding: 16px;
            line-height: 1.6;
        }

        .ai-main-card {
            background: #111827;
            border: 1px solid #334155;
            border-radius: 22px;
            padding: 28px;
            flex: 1;
            overflow: auto;
        }

        .ai-badge {
            display: inline-block;
            background: #2563eb;
            padding: 12px 20px;
            border-radius: 14px;
            margin-bottom: 25px;
        }

        .chat-area {
            margin-top: 20px;
            background: #020617;
            border: 1px solid #334155;
            border-radius: 14px;
            min-height: 220px;
            padding: 16px;
        }

        .workspace-input {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .workspace-input input {
            flex: 1;
            padding: 15px;
            border-radius: 12px;
            border: none;
            background: #1e293b;
            color: white;
        }

        .workspace-input button {
            background: #22c55e;
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0 28px;
            cursor: pointer;
        }

        .studio-card {
            background: #1f2937;
            border: 1px solid #334155;
            padding: 18px;
            border-radius: 16px;
            margin-bottom: 16px;
        }

        .studio-card h3 {
            margin: 0 0 8px 0;
        }

        .studio-card p {
            color: #94a3b8;
            margin: 0;
        }

        .submit-exercise {
            margin-top: 20px;
        }

        .submit-exercise textarea {
            width: 100%;
            height: 130px;
            border-radius: 12px;
            border: none;
            padding: 12px;
            box-sizing: border-box;
        }

        .submit-exercise button {
            margin-top: 10px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 10px;
            padding: 12px 18px;
            cursor: pointer;
        }

        .back-link {
            color: #60a5fa;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .workspace-user-message {
            background: #2563eb;
            padding: 12px;
            border-radius: 12px;
            margin: 10px 0;
            margin-left: auto;
            width: fit-content;
            max-width: 75%;
            line-height: 1.5;
        }

        .workspace-bot-message {
            background: #1e293b;
            padding: 12px;
            border-radius: 12px;
            margin: 10px 0;
            width: fit-content;
            max-width: 75%;
            line-height: 1.5;
        }

        .chat-area {
            overflow-y: auto;
        }

        .guide-import-box {
            background: #020617;
            border: 1px solid #334155;
            border-radius: 14px;
            padding: 14px;
            margin: 18px 0;
        }

        .guide-import-box h3 {
            margin-top: 0;
            font-size: 18px;
        }

        .guide-import-box input[type="file"] {
            width: 100%;
            margin-bottom: 12px;
            color: white;
        }

        .guide-import-box button {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 12px;
            background: #2563eb;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .guide-import-box button:hover {
            background: #1d4ed8;
        }

        .small-note {
            color: #94a3b8;
            font-size: 13px;
            margin-bottom: 0;
        }

        .guide-card {
            background: #020617;
            border: 1px solid #475569;
            border-radius: 14px;
            padding: 16px;
            line-height: 1.7;
            margin-bottom: 22px;
        }
    </style>
</head>

<body>

    <div class="workspace">

        <!-- LEFT -->
        <div class="left-panel">
            <a class="back-link" href="giaoDien.php">← Quay lại</a>

            <h1><?php echo htmlspecialchars($ai['name']); ?></h1>

            <input class="search-box" placeholder="Tìm trong hướng dẫn...">

            <div class="guide-import-box">
                <h3>📥 Nhập hướng dẫn từ file</h3>
                <input
                    type="file"
                    id="guideFile"
                    accept=".txt,.md,.csv,.json,.html,.docx">

                <button type="button" onclick="importGuideFile()">
                    AI quét file
                </button>

                <p class="small-note">
                    Hỗ trợ: txt, md, csv, json, html, docx
                </p>
            </div>

            <h2>📘 Hướng dẫn sử dụng</h2>
            <div class="guide-card">
                <?php echo nl2br(htmlspecialchars($ai['guide'])); ?>
            </div>

            <h2>✨ Prompt mẫu</h2>
            <div class="guide-card">
                <?php echo nl2br(htmlspecialchars($ai['prompt_template'])); ?>
            </div>

            <h2>🎯 Tình huống sử dụng</h2>
            <div class="guide-card">
                <?php echo nl2br(htmlspecialchars($ai['use_cases'])); ?>
            </div>

            <h2>🧪 Bài tập thực hành</h2>
            <div class="guide-card">
                <?php echo nl2br(htmlspecialchars($ai['exercises'])); ?>
            </div>

            <h2>⚠️ Điểm yếu</h2>
            <div class="guide-card">
                <?php echo nl2br(htmlspecialchars($ai['weaknesses'])); ?>
            </div>
        </div>

        <!-- CENTER -->
        <div class="center-panel">
            <div class="ai-main-card">
                <div class="ai-badge">
                    <?php echo htmlspecialchars($ai['name']); ?>
                </div>

                <h1>AI Learning Workspace</h1>

                <p>
                    Đây là khu vực thực hành với
                    <b><?php echo htmlspecialchars($ai['name']); ?></b>.
                    Hiện tại phần hỏi đáp đang chạy mô phỏng. Sau này có thể tích hợp API riêng.
                </p>

                <div class="chat-area" id="workspaceChat">
                    <p>
                        Hãy nhập câu hỏi, prompt hoặc bài tập ở khung bên dưới.
                        Hệ thống sẽ lưu quá trình học tập của bạn.
                    </p>
                </div>

                <div class="workspace-input">
                    <input id="workspaceInput" placeholder="Nhập câu hỏi hoặc prompt thực hành...">
                    <button onclick="workspaceSend()">Gửi</button>
                </div>

                <!-- <div class="submit-exercise">
                <h2>🧩 Nộp bài tập thực hành</h2>

                <form action="../api/exercises/save_result.php" method="POST">
                    <input type="hidden" name="ai_tool_id" value="<?php echo intval($ai['id']); ?>">
                    <input type="hidden" name="exercise_title" value="Bài tập với <?php echo htmlspecialchars($ai['name']); ?>">

                    <textarea name="user_answer" placeholder="Nhập bài làm của bạn..." required></textarea>

                    <button type="submit">
                        Nộp và chấm điểm
                    </button>
                </form>
            </div> -->
            </div>
        </div>

        <!-- RIGHT -->
        <div class="right-panel">
            <h1>Studio</h1>

            <?php if (count($useCases) > 0) { ?>
                <?php foreach ($useCases as $case) { ?>
                    <div class="studio-card">
                        <h3><?php echo htmlspecialchars($case); ?></h3>
                        <p>Chức năng phù hợp với <?php echo htmlspecialchars($ai['name']); ?></p>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="studio-card">
                    <h3>Tổng quan</h3>
                    <p>Tạo bản tóm tắt AI</p>
                </div>

                <div class="studio-card">
                    <h3>Báo cáo</h3>
                    <p>Tạo báo cáo tự động</p>
                </div>

                <div class="studio-card">
                    <h3>Bài kiểm tra</h3>
                    <p>Tạo quiz luyện tập</p>
                </div>

                <div class="studio-card">
                    <h3>Bảng dữ liệu</h3>
                    <p>Phân tích dữ liệu AI</p>
                </div>
            <?php } ?>

            <h2>🎯 Bài tập gợi ý</h2>

            <?php if (count($exercises) > 0) { ?>
                <?php foreach ($exercises as $ex) { ?>
                    <div class="studio-card">
                        <p><?php echo htmlspecialchars($ex); ?></p>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <div class="studio-card">
                    <p>Hãy viết một prompt dùng AI này để giải quyết một nhiệm vụ học tập cụ thể.</p>
                </div>
            <?php } ?>
        </div>

    </div>

    <script>
        const AI_TOOL_ID = <?php echo intval($ai['id']); ?>;

        function escapeHTML(str) {
            return String(str)
                .replaceAll("&", "&amp;")
                .replaceAll("<", "&lt;")
                .replaceAll(">", "&gt;")
                .replaceAll('"', "&quot;")
                .replaceAll("'", "&#039;");
        }

        function appendWorkspaceMessage(sender, content) {
            const chat = document.getElementById("workspaceChat");

            const div = document.createElement("div");

            div.className = sender === "user" ?
                "workspace-user-message" :
                "workspace-bot-message";

            div.innerHTML = escapeHTML(content).replace(/\n/g, "<br>");

            chat.appendChild(div);
            chat.scrollTop = chat.scrollHeight;
        }

        function saveWorkspaceMessage(sender, content) {
            return fetch("../api/workspace/save_message.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "ai_tool_id=" + encodeURIComponent(AI_TOOL_ID) +
                        "&sender=" + encodeURIComponent(sender) +
                        "&content=" + encodeURIComponent(content)
                })
                .then(res => res.json());
        }

        function loadWorkspaceMessages() {
            fetch("../api/workspace/get_messages.php?ai_tool_id=" + encodeURIComponent(AI_TOOL_ID), {
                    credentials: "same-origin"
                })
                .then(res => res.json())
                .then(data => {
                    const chat = document.getElementById("workspaceChat");
                    chat.innerHTML = "";

                    if (!data || data.length === 0) {
                        appendWorkspaceMessage(
                            "bot",
                            "Chào bạn \nHãy nhập câu hỏi, prompt hoặc bài tập cần luyện."
                        );
                        return;
                    }

                    data.forEach(msg => {
                        appendWorkspaceMessage(msg.sender, msg.content);
                    });
                });
        }

        function workspaceSend() {
            const input = document.getElementById("workspaceInput");
            const text = input.value.trim();

            if (text === "") {
                return;
            }

            input.value = "";

            appendWorkspaceMessage("user", text);

            saveWorkspaceMessage("user", text)
                .then(() => {
                    appendWorkspaceMessage("bot", "Đang suy nghĩ...");

                    return fetch("../api/workspace/ollama_chat.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "ai_tool_id=" + encodeURIComponent(AI_TOOL_ID) +
                            "&prompt=" + encodeURIComponent(text)
                    });
                })
                .then(res => res.json())
                .then(data => {
                    const chat = document.getElementById("workspaceChat");
                    const last = chat.lastElementChild;

                    if (last && last.innerText === "Đang suy nghĩ...") {
                        last.remove();
                    }

                    if (data.error) {
                        appendWorkspaceMessage("bot", "Lỗi AI: " + data.error);
                        return saveWorkspaceMessage("bot", "Lỗi AI: " + data.error);
                    }

                    const reply = data.reply || "Không nhận được phản hồi từ Ollama.";

                    appendWorkspaceMessage("bot", reply);

                    return saveWorkspaceMessage("bot", reply);
                })
                .catch(err => {
                    const chat = document.getElementById("workspaceChat");
                    const last = chat.lastElementChild;

                    if (last && last.innerText === "Đang suy nghĩ...") {
                        last.remove();
                    }

                    appendWorkspaceMessage("bot", "Lỗi fetch: " + err.message);
                });
        }

        function importGuideFile() {
            const fileInput = document.getElementById("guideFile");

            if (!fileInput.files || fileInput.files.length === 0) {
                alert("Bệ hạ hãy chọn file hướng dẫn trước.");
                return;
            }

            const formData = new FormData();
            formData.append("ai_tool_id", AI_TOOL_ID);
            formData.append("guide_file", fileInput.files[0]);

            appendWorkspaceMessage(
                "bot",
                "Đang quét file và hệ thống hóa hướng dẫn..."
            );

            fetch("../api/ai/import_guide.php", {
                    method: "POST",
                    body: formData,
                    credentials: "same-origin"
                })
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        appendWorkspaceMessage(
                            "bot",
                            "Lỗi nhập hướng dẫn: " + data.error
                        );

                        if (data.raw) {
                            console.log(data.raw);
                        }

                        return;
                    }

                    appendWorkspaceMessage(
                        "bot",
                        "Đã tạo bản hướng dẫn hoàn chỉnh. Trang sẽ tải lại để cập nhật."
                    );

                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                })
                .catch(err => {
                    appendWorkspaceMessage(
                        "bot",
                        "Lỗi fetch: " + err.message
                    );
                });
        }

        document.getElementById("workspaceInput")
            .addEventListener("keypress", function(e) {
                if (e.key === "Enter") {
                    workspaceSend();
                }
            });

        loadWorkspaceMessages();
    </script>

</body>

</html>