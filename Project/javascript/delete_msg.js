function delete_msg_fun(msg_id){
    let oldPopup = document.getElementById("chatDeletePopup");
    if(oldPopup) oldPopup.remove();

    let popup = document.createElement("div");
    popup.id = "chatDeletePopup";
    popup.innerHTML = `
        <div class="mini-delete-box">
            <div class="mini-delete-text">Delete this message?</div>
            <div class="mini-delete-actions">
                <button class="mini-cancel-btn">Cancel</button>
                <button class="mini-delete-btn">Delete</button>
            </div>
        </div>
    `;

    document.getElementById("chat-box").appendChild(popup);
    chatBox.scrollTop = chatBox.scrollHeight;

    popup.querySelector(".mini-cancel-btn").onclick = () => popup.remove();

    popup.querySelector(".mini-delete-btn").onclick = () => {
        fetch("api/delete_msg.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "msg_id=" + msg_id
        })
        .then(res => res.text())
        .then(() => { popup.remove(); fetchMsgs(); })
        .catch(err => console.log(err));
    };
}