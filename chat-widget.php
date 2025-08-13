
<div id="chatWidget" class="chat-widget">
    <!-- Chat Button -->
    <div id="chatButton" class="w-16 h-16 bg-primary rounded-full shadow-lg cursor-pointer flex items-center justify-center text-white hover:bg-secondary transition-colors">
        <i class="fas fa-comments text-xl"></i>
    </div>
    
    <!-- Chat Window -->
    <div id="chatWindow" class="hidden fixed bottom-20 right-5 w-80 h-96 bg-white rounded-lg shadow-2xl border border-gray-200 flex flex-col">
        <!-- Chat Header -->
        <div class="bg-primary text-white p-4 rounded-t-lg flex items-center justify-between">
            <div>
                <h3 class="font-semibold">Live Chat</h3>
                <p class="text-xs opacity-90">Tim support siap membantu</p>
            </div>
            <button id="closeChatButton" class="text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Chat Form (Initial) -->
        <div id="chatForm" class="p-4 flex-1 flex flex-col justify-center">
            <div class="text-center mb-4">
                <div class="w-12 h-12 bg-primary/10 rounded-full mx-auto mb-2 flex items-center justify-center">
                    <i class="fas fa-user text-primary"></i>
                </div>
                <h4 class="font-semibold text-gray-900">Mulai Percakapan</h4>
                <p class="text-sm text-gray-600">Isi data di bawah untuk memulai chat</p>
            </div>
            
            <form id="startChatForm">
                <div class="mb-3">
                    <input type="text" id="visitorName" placeholder="Nama Anda" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary text-sm">
                </div>
                <div class="mb-4">
                    <input type="email" id="visitorEmail" placeholder="Email Anda" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary text-sm">
                </div>
                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white py-2 rounded-lg font-medium text-sm">
                    Mulai Chat
                </button>
            </form>
        </div>
        
        <!-- Chat Messages -->
        <div id="chatMessages" class="hidden flex-1 flex flex-col">
            <div class="flex-1 p-4 overflow-y-auto" id="messagesContainer">
                <!-- Messages will be loaded here -->
            </div>
            
            <!-- Message Input -->
            <div class="p-3 border-t border-gray-200">
                <form id="sendMessageForm" class="flex space-x-2">
                    <input type="text" id="messageInput" placeholder="Ketik pesan..." 
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary text-sm">
                    <button type="submit" class="bg-primary hover:bg-secondary text-white px-3 py-2 rounded-lg">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
class ChatWidget {
    constructor() {
        this.sessionId = localStorage.getItem('chat_session_id');
        this.visitorName = localStorage.getItem('visitor_name');
        this.visitorEmail = localStorage.getItem('visitor_email');
        this.isOpen = false;
        this.messageCheckInterval = null;
        this.lastMessageId = 0;
        
        this.initializeElements();
        this.bindEvents();
        
        if (this.sessionId) {
            this.showChatMessages();
            this.startMessagePolling();
        }
    }
    
    initializeElements() {
        this.chatButton = document.getElementById('chatButton');
        this.chatWindow = document.getElementById('chatWindow');
        this.closeChatButton = document.getElementById('closeChatButton');
        this.chatForm = document.getElementById('chatForm');
        this.chatMessages = document.getElementById('chatMessages');
        this.messagesContainer = document.getElementById('messagesContainer');
        this.startChatForm = document.getElementById('startChatForm');
        this.sendMessageForm = document.getElementById('sendMessageForm');
        this.messageInput = document.getElementById('messageInput');
    }
    
    bindEvents() {
        this.chatButton.addEventListener('click', () => this.toggleChat());
        this.closeChatButton.addEventListener('click', () => this.closeChat());
        this.startChatForm.addEventListener('submit', (e) => this.startChat(e));
        this.sendMessageForm.addEventListener('submit', (e) => this.sendMessage(e));
    }
    
    toggleChat() {
        if (this.isOpen) {
            this.closeChat();
        } else {
            this.openChat();
        }
    }
    
    openChat() {
        this.chatWindow.classList.remove('hidden');
        this.isOpen = true;
        this.chatButton.innerHTML = '<i class="fas fa-times text-xl"></i>';
    }
    
    closeChat() {
        this.chatWindow.classList.add('hidden');
        this.isOpen = false;
        this.chatButton.innerHTML = '<i class="fas fa-comments text-xl"></i>';
    }
    
    async startChat(e) {
        e.preventDefault();
        
        const name = document.getElementById('visitorName').value;
        const email = document.getElementById('visitorEmail').value;
        
        const formData = new FormData();
        formData.append('action', 'start_session');
        formData.append('name', name);
        formData.append('email', email);
        
        try {
            const response = await fetch('api/chat.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.sessionId = result.data.session_id;
                this.visitorName = name;
                this.visitorEmail = email;
                
                localStorage.setItem('chat_session_id', this.sessionId);
                localStorage.setItem('visitor_name', name);
                localStorage.setItem('visitor_email', email);
                
                this.showChatMessages();
                this.startMessagePolling();
            } else {
                alert('Gagal memulai chat: ' + result.message);
            }
        } catch (error) {
            console.error('Error starting chat:', error);
            alert('Terjadi kesalahan saat memulai chat');
        }
    }
    
    showChatMessages() {
        this.chatForm.classList.add('hidden');
        this.chatMessages.classList.remove('hidden');
        this.loadMessages();
    }
    
    async loadMessages() {
        if (!this.sessionId) return;
        
        try {
            const response = await fetch(`api/chat.php?action=get_messages&session_id=${this.sessionId}&last_message_id=${this.lastMessageId}`);
            const result = await response.json();
            
            if (result.success && result.data.length > 0) {
                result.data.forEach(message => {
                    this.displayMessage(message);
                    this.lastMessageId = Math.max(this.lastMessageId, parseInt(message.id));
                });
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }
    
    displayMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `mb-3 ${message.sender_type === 'visitor' ? 'text-right' : 'text-left'}`;
        
        const bubbleClass = message.sender_type === 'visitor' 
            ? 'bg-primary text-white ml-8' 
            : 'bg-gray-100 text-gray-900 mr-8';
            
        messageDiv.innerHTML = `
            <div class="inline-block ${bubbleClass} rounded-lg px-3 py-2 text-sm max-w-xs">
                ${this.escapeHtml(message.message)}
            </div>
            <div class="text-xs text-gray-500 mt-1">
                ${message.sender_name || (message.sender_type === 'visitor' ? 'Anda' : 'Admin')} • 
                ${new Date(message.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}
            </div>
        `;
        
        this.messagesContainer.appendChild(messageDiv);
    }
    
    async sendMessage(e) {
        e.preventDefault();
        
        const message = this.messageInput.value.trim();
        if (!message) return;
        
        const formData = new FormData();
        formData.append('action', 'send_message');
        formData.append('session_id', this.sessionId);
        formData.append('sender_type', 'visitor');
        formData.append('sender_name', this.visitorName);
        formData.append('sender_email', this.visitorEmail);
        formData.append('message', message);
        
        try {
            const response = await fetch('api/chat.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.messageInput.value = '';
                this.displayMessage(result.data);
                this.lastMessageId = Math.max(this.lastMessageId, parseInt(result.data.id));
                this.scrollToBottom();
            } else {
                alert('Gagal mengirim pesan: ' + result.message);
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Terjadi kesalahan saat mengirim pesan');
        }
    }
    
    startMessagePolling() {
        this.messageCheckInterval = setInterval(() => {
            this.loadMessages();
        }, 3000); // Check for new messages every 3 seconds
    }
    
    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize chat widget when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ChatWidget();
});
</script>
