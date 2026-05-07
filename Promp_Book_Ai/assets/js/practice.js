// 1. Nhận diện danh tính AI từ URL
const params = new URLSearchParams(window.location.search);
const aiName = params.get('ai');
document.getElementById('aiNameDisplay').innerText = aiName;

// 2. Mô phỏng việc tải bài tập (có thể thay bằng API thật)
window.onload = () => {
    document.getElementById('exerciseContent').innerText = 
        `Bài tập: Hãy viết một Prompt cho ${aiName} để yêu cầu nó tóm tắt một đoạn văn bản dài 1000 chữ thành 3 gạch đầu dòng ngắn gọn.`;
    
    document.getElementById('promptHint').innerText = 
        `"Hãy đóng vai chuyên gia tóm tắt, hãy lược bỏ... và giữ lại..."`;
};

// 3. Triệu hồi AI kiểm tra câu trả lời
async function submitToAI() {
    const input = document.getElementById('userInput').value;
    const responseBox = document.getElementById('aiResponse');

    if (!input) return;

    responseBox.innerHTML = "Đang liên kết AI...";

    try {
        // Tận dụng file check_prompt.php đã có ở API
        const res = await fetch('../api/ai/check_prompt.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ prompt: input, ai: aiName })
        });
        const result = await res.json();
        
        // Hiển thị đánh giá thông minh
        responseBox.innerHTML = `
            <div style="color: #10b981; font-weight: bold;">Đánh giá của ${aiName}:</div>
        `;
    } catch (err) {
        responseBox.innerHTML = "Không thể kết nối với database.";
    }
}