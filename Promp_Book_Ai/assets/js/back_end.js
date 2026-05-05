let currentConvId = null;

const sendBtn = document.getElementById('sendBtn');
const inputText = document.getElementById('inputText');
const messagesDiv = document.getElementById('messages');
const historyList = document.getElementById('historyList');

// 1. HÀM GỬI TIN NHẮN (SỬA LỖI KHÔNG GỬI ĐƯỢC) 
async function sendMessage() {
    const text = inputText.value.trim();
    if (!text) return;

    // Tự động tạo cuộc trò chuyện nếu là tin nhắn đầu tiên
    if (!currentConvId) {
        try {
            const res = await fetch('../api/conversations/create.php');
            currentConvId = await res.text();
        } catch (err) {
            console.error("Không thể khai thông cuộc trò chuyện:", err);
            return;
        }
    }

    // Hiển thị tin nhắn 
    appendMessage('user', text);
    inputText.value = '';

    try {
        // Lưu tin nhắn
        await fetch('../api/messages/save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `conv_id=${currentConvId}&sender=user&content=${encodeURIComponent(text)}`
        });

        // Cập nhật tiêu đề cuộc trò chuyện ngay lập tức
        const title = text.substring(0, 20);
        await fetch('../api/conversations/update_title.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${currentConvId}&title=${encodeURIComponent(title)}`
        });

        // Lấy AI
        const response = await fetch(`../api/ai/suggest.php?q=${encodeURIComponent(text)}`);
        const data = await response.json();
        
        let aiReply = "Đang xử lý yêu cầu";
        if (data && data.length > 0) {
            aiReply = `Gợi ý dành cho bạn: ${data[0].name}. (Đánh giá: ${data[0].rating} sao)`;
        }

        appendMessage('bot', aiReply);

        // Lưu lời đáp của AI
        await fetch('../api/messages/save.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `conv_id=${currentConvId}&sender=bot&content=${encodeURIComponent(aiReply)}`
        });

        // Làm mới danh sách lịch sử
        loadHistory();

    } catch (error) {
        appendMessage('bot', "Hệ thống đang xử lý");
    }
}

// --- 2. HÀM LỊCH SỬ (CHỈ HIỆN CHAT TỪ DATABASE) ---
async function loadHistory() {
    try {
        const res = await fetch('../api/conversations/get.php');
        const list = await res.json();

        historyList.innerHTML = ''; // Xóa bỏ các mục mặc định như "Dự án mới", "Phân tích"...
        
        if (list.length === 0) {
            historyList.innerHTML = '<li style="font-style: italic; opacity: 0.5;">Chưa có sử sách...</li>';
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

// --- 3. TẢI LẠI ĐOẠN CHAT CŨ ---
async function loadDetailChat(id) {
    currentConvId = id;
    messagesDiv.innerHTML = ''; 
    const res = await fetch(`../api/messages/get.php?id=${id}`);
    const msgs = await res.json();
    msgs.forEach(m => appendMessage(m.sender, m.content));
}

// --- 4. CÁC HÀM ĐIỀU HƯỚNG (SỬA LỖI ĐĂNG NHẬP) ---
function goLogin() {
    // Vì Bệ hạ đang ở /views/, cần quay lại gốc rồi vào /Login/
    window.location.href = '../Login/dangnhap.php';
}

function goRegister() {
    window.location.href = '../Login/dangky.php';
}

function newChat() {
    currentConvId = null;
    messagesDiv.innerHTML = `
        <div class="message-row">
            <div class="avatar">🤖</div>
            <div class="bubble bot-bubble">Rất vui được gặp bạn, Tôi có thể giúp gì cho bạn hôm nay?</div>
        </div>`;
}

// --- 5. KHỞI CHẠY ---
sendBtn.onclick = sendMessage;
inputText.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') sendMessage();
});

// Gán hàm vào window để các thuộc tính onclick trong HTML có thể gọi được
window.goLogin = goLogin;
window.goRegister = goRegister;
window.newChat = newChat;
window.onload = loadHistory;

function appendMessage(sender, content) {
    const row = document.createElement('div');
    row.className = `message-row ${sender === 'user' ? 'user-row' : ''}`;
    const avatar = sender === 'user' ? '👤' : '🤖';
    const bubbleClass = sender === 'user' ? 'user-bubble' : 'bot-bubble';

    row.innerHTML = `
        ${sender === 'bot' ? `<div class="avatar">${avatar}</div>` : ''}
        <div class="bubble ${bubbleClass}">${content}</div>
        ${sender === 'user' ? `<div class="avatar">${avatar}</div>` : ''}
    `;
    messagesDiv.appendChild(row);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}