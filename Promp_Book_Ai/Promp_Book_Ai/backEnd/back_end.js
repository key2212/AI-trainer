document.addEventListener("DOMContentLoaded", function () {

let currentConv = 0;
let isLock = false;

function updateTitle(id, text) {
    fetch("History/update_title.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `id=${id}&title=${encodeURIComponent(text.substring(0,30))}`
    })
    .then(res => res.text())
    .then(data => {
        console.log("UPDATE TITLE:", data); // 👈 kiểm tra tại đây
    })
    .catch(err => {
        console.error("Lỗi update title:", err);
    });
}

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
    });
}

// LOAD MESSAGES
function loadMessages(id) {
    fetch("History/get_messages.php?id=" + id)
    .then(res => res.json())
    .then(data => {
        const box = document.getElementById("messages");
        box.innerHTML = "";
        data.forEach(msg => appendMessage(msg.sender, msg.content));
        box.scrollTop = box.scrollHeight;
    });
}

// SAVE MESSAGE
function saveMessage(text) {
    fetch("History/save_message.php", {
        method: "POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: `conv_id=${currentConv}&sender=user&content=${encodeURIComponent(text)}`
    })
    .then(res => res.text())
    .then(data => {
        console.log("SAVE:", data);

        isLock = false; 

        loadMessages(currentConv);
        loadConversations();
    })
    .catch(err => {
        console.error("Lỗi save:", err);
        isLock = false; 
    });
}

// SEND
function send() {
    const input = document.getElementById("inputText");
    const text = input.value.trim();

    if (text === "" || isLock) return;

    isLock = true;
    appendMessage("user", text);
    input.value = "";

    if (currentConv === 0) {
        fetch("History/create_conversation.php")
        .then(res => res.text())
        .then(id => {
            currentConv = parseInt(id);

            if (!currentConv) {
                alert("Tạo hội thoại lỗi!");
                isLock = false;
                return;
            }

            updateTitle(currentConv, text);
            saveMessage(text);
        })
        .catch(err => {
            console.error("Lỗi create:", err);
            isLock = false; 
        });

    } else {
        saveMessage(text);
    }
}

// UI
function appendMessage(sender, content) {
    const box = document.getElementById("messages");
    const div = document.createElement("div");
    div.className = "message " + sender;
    div.textContent = content;
    box.appendChild(div);
    box.scrollTop = box.scrollHeight;
}

// MENU
document.getElementById("menuBtn").addEventListener("click", function(e){
    e.stopPropagation();
    let menu = document.getElementById("userMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
});

window.addEventListener("click", function(e){
    if (!e.target.closest('#menuBtn') && !e.target.closest('#userMenu')) {
        document.getElementById("userMenu").style.display = "none";
    }
});

// ACTIONS
window.newChat = function(){
    currentConv = 0;
    isLock = false;
    document.getElementById("messages").innerHTML =
    '<div class="message bot">Bắt đầu cuộc trò chuyện mới </div>';
}

window.newProject = function(){
    alert("Đang phát triển ");
}

window.goLogin = function(){
    window.location.href = "Login/dangnhap.php";
}

window.goRegister = function(){
    window.location.href = "Login/dangky.php";
}

// EVENT
document.getElementById("sendBtn").addEventListener("click", send);
document.getElementById("inputText").addEventListener("keypress", e=>{
    if(e.key==="Enter") send();
});

// INIT
loadConversations();

});