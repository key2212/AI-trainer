document.addEventListener("DOMContentLoaded", function(){
let currentConv = 0;
let isLock = false; // Chặn gửi tin trùng lặp

// LOAD SIDEBAR 
function loadConversations() {
    fetch("History/get_conversations.php")
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById("historyList");
            list.innerHTML = "";
            data.forEach(conv => {
                let li = document.createElement("li");  
                li.textContent = conv.title || "Hội thoại #" + conv.id;
                li.onclick = () => {
                    currentConv = parseInt(conv.id);
                    loadMessages(conv.id);
                };
                list.appendChild(li);
            });
        })
        .catch(err => console.error("Lỗi load sidebar:", err));
}

// Tải TIN NHẮN 
function loadMessages(id) {
    fetch("History/get_messages.php?id=" + id)
        .then(res => res.json())
        .then(data => {
            const box = document.getElementById("messages");
            box.innerHTML = "";
            data.forEach(msg => {
                appendMessage(msg.sender, msg.content);
            });
        });
}

// GỬI TIN NHẮN 
function send() {
    const input = document.getElementById("inputText");
    const text = input.value.trim();

    if (text === "" || isLock) return;

    isLock = true; // Khóa gửi tin
    appendMessage("user", text);
    input.value = "";

    if (currentConv === 0) {
        fetch("History/create_conversation.php")
            .then(res => res.text())
            .then(id => {
                currentConv = parseInt(id);
                updateTitle(currentConv, text);
                saveMessage(text);
            });
    } else {
        saveMessage(text); // 🔥 bắt buộc phải có
    }
}


// LƯU VÀO DB 
function saveMessage(text) {
    fetch("History/save_message.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `conv_id=${currentConv}&sender=user&content=${encodeURIComponent(text)}`
    })
    .then(() => {
        isLock = false; // Mở khóa
        loadMessages(currentConv);  // Hiện lại khi click
        loadConversations(); // Cập nhật lại sidebar (để hiện tiêu đề mới)
    });
}

//  BỔ TRỢ UI 
function appendMessage(sender, content) {
    const box = document.getElementById("messages");
    const div = document.createElement("div");
    div.className = "message " + sender;
    div.textContent = content;
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
}

function newChat() {
    currentConv = 0;
    document.getElementById("messages").innerHTML = '<div class="message bot">Sẵn sàng cho cuộc hội thoại mới!</div>';
}


// Cập nhật tiêu đề
function updateTitle(id, text){
    fetch("History/update_title.php",{
        method:"POST",
        headers:{
            "Content-Type":"application/x-www-form-urlencoded"
        },
        body:`id=${id}&title=${encodeURIComponent(text)}`
    });
}

function toggleMenu(){
    let menu = document.getElementById("userMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}

// Click ra ngoài thì tự đóng (xịn hơn)
window.onclick = function(e){
    if(!e.target.matches('.top-bar')){
        document.getElementById("userMenu").style.display = "none";
    }
}
// Demo chức năng
function login(){
    alert("Đi tới trang đăng nhập");
}

function register(){
    alert("Đi tới trang đăng ký");
}
// Enter xuống dòng
document.getElementById("inputText").addEventListener("keypress", (e) => {
    if (e.key === "Enter") send();
}); 

// Chạy khi Tải trang
loadConversations();
});

