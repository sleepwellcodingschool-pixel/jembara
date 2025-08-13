
class LiveChatApp {
    constructor() {
        this.currentSessionId = null;
        this.currentUser = null;
        this.sessions = [];
        this.messages = [];
        this.lastMessageId = 0;
        this.refreshInterval = null;
        this.isOnline = navigator.onLine;
        this.notificationPermission = 'default';
        this.serviceWorker = null;
        
        this.init();
    }
    
    async init() {
        // Check if user is already logged in
        const adminData = localStorage.getItem('admin_data');
        if (adminData) {
            try {
                this.currentUser = JSON.parse(adminData);
                this.showMainApp();
            } catch (e) {
                this.showLogin();
            }
        } else {
            this.showLogin();
        }
        
        this.bindEvents();
        this.checkOnlineStatus();
        await this.initServiceWorker();
        this.requestNotificationPermission();
    }
    
    bindEvents() {
        // Login form
        document.getElementById('loginForm').addEventListener('submit', (e) => this.handleLogin(e));
        
        // Logout button
        document.getElementById('logoutBtn').addEventListener('click', () => this.handleLogout());
        
        // Message form
        document.getElementById('messageForm').addEventListener('submit', (e) => this.sendMessage(e));
        
        // Refresh button
        document.getElementById('refreshBtn').addEventListener('click', () => this.refreshData());
        
        // Close chat button
        document.getElementById('closeChatBtn').addEventListener('click', () => this.closeCurrentChat());
        
        // Mark read button
        document.getElementById('markReadBtn').addEventListener('click', () => this.markCurrentChatAsRead());
        
        // Notification buttons
        document.getElementById('enableNotifications').addEventListener('click', () => this.enableNotifications());
        document.getElementById('dismissNotifications').addEventListener('click', () => this.dismissNotificationRequest());
        
        // Online/offline events
        window.addEventListener('online', () => this.handleOnlineStatus(true));
        window.addEventListener('offline', () => this.handleOnlineStatus(false));
        
        // Visibility change for notifications
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                this.refreshData();
            }
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'r') {
                e.preventDefault();
                this.refreshData();
            }
        });
    }
    
    showLogin() {
        document.getElementById('loadingScreen').style.display = 'none';
        document.getElementById('loginModal').classList.remove('hidden');
        document.getElementById('mainApp').classList.add('hidden');
    }
    
    showMainApp() {
        document.getElementById('loadingScreen').style.display = 'none';
        document.getElementById('loginModal').classList.add('hidden');
        document.getElementById('mainApp').classList.remove('hidden');
        
        this.startRefreshInterval();
        this.loadSessions();
    }
    
    async handleLogin(e) {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        try {
            const response = await fetch('../api/auth.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'login',
                    username: username,
                    password: password
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.currentUser = result.data;
                localStorage.setItem('admin_data', JSON.stringify(result.data));
                this.showMainApp();
            } else {
                this.showLoginError(result.message);
            }
        } catch (error) {
            console.error('Login error:', error);
            this.showLoginError('Terjadi kesalahan saat login');
        }
    }
    
    handleLogout() {
        if (confirm('Yakin ingin keluar?')) {
            localStorage.removeItem('admin_data');
            this.currentUser = null;
            this.stopRefreshInterval();
            this.showLogin();
        }
    }
    
    showLoginError(message) {
        const errorDiv = document.getElementById('loginError');
        const errorText = document.getElementById('loginErrorText');
        errorText.textContent = message;
        errorDiv.classList.remove('hidden');
        
        setTimeout(() => {
            errorDiv.classList.add('hidden');
        }, 5000);
    }
    
    async loadSessions() {
        try {
            const response = await fetch(`../api/chat.php?action=get_sessions&admin_id=${this.currentUser.id}`);
            const result = await response.json();
            
            if (result.success) {
                this.sessions = result.data;
                this.renderSessions();
                this.updateActiveChatsCount();
            }
        } catch (error) {
            console.error('Error loading sessions:', error);
        }
    }
    
    renderSessions() {
        const sessionsList = document.getElementById('sessionsList');
        const emptyState = document.getElementById('emptyState');
        
        if (this.sessions.length === 0) {
            emptyState.style.display = 'block';
            return;
        }
        
        emptyState.style.display = 'none';
        
        // Clear existing sessions except empty state
        Array.from(sessionsList.children).forEach(child => {
            if (child.id !== 'emptyState') {
                child.remove();
            }
        });
        
        this.sessions.forEach(session => {
            const sessionDiv = this.createSessionElement(session);
            sessionsList.appendChild(sessionDiv);
        });
    }
    
    createSessionElement(session) {
        const div = document.createElement('div');
        div.className = `session-item p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors ${this.currentSessionId === session.session_id ? 'bg-primary/5 border-primary/20' : ''}`;
        div.dataset.sessionId = session.session_id;
        
        div.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-primary/10 text-primary rounded-full flex items-center justify-center">
                    <i class="fas fa-user"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="font-medium text-gray-900 truncate">
                            ${session.visitor_name || 'Pengunjung'}
                        </h4>
                        ${session.unread_count > 0 ? `<span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">${session.unread_count}</span>` : ''}
                    </div>
                    <p class="text-sm text-gray-600 truncate">
                        ${session.visitor_whatsapp || 'WhatsApp tidak tersedia'}
                    </p>
                    ${session.last_message ? `<p class="text-xs text-gray-500 truncate mt-1">${session.last_message.substring(0, 50)}...</p>` : ''}
                    <p class="text-xs text-gray-400 mt-1">
                        ${this.timeAgo(session.last_message_time || session.created_at)}
                    </p>
                </div>
            </div>
        `;
        
        div.addEventListener('click', () => this.selectSession(session));
        
        return div;
    }
    
    async selectSession(session) {
        this.currentSessionId = session.session_id;
        
        // Update UI
        document.querySelectorAll('.session-item').forEach(item => {
            item.classList.remove('bg-primary/5', 'border-primary/20');
        });
        document.querySelector(`[data-session-id="${session.session_id}"]`).classList.add('bg-primary/5', 'border-primary/20');
        
        // Show chat area
        document.getElementById('noChatSelected').classList.add('hidden');
        document.getElementById('chatArea').classList.remove('hidden');
        
        // Update chat header
        document.getElementById('currentUserName').textContent = session.visitor_name || 'Pengunjung';
        document.getElementById('currentUserWhatsapp').textContent = session.visitor_whatsapp || 'WhatsApp tidak tersedia';
        
        // Load messages
        await this.loadMessages(session.session_id);
    }
    
    async loadMessages(sessionId) {
        try {
            const response = await fetch(`../api/chat.php?action=get_messages&session_id=${sessionId}&last_message_id=${this.lastMessageId}`);
            const result = await response.json();
            
            if (result.success) {
                if (sessionId !== this.currentSessionId) {
                    // Messages for a different session, ignore
                    return;
                }
                
                this.messages = result.data;
                this.renderMessages();
                this.scrollToBottom();
                
                if (result.data.length > 0) {
                    this.lastMessageId = Math.max(...result.data.map(m => parseInt(m.id)));
                }
            }
        } catch (error) {
            console.error('Error loading messages:', error);
        }
    }
    
    renderMessages() {
        const messagesArea = document.getElementById('messagesArea');
        messagesArea.innerHTML = '';
        
        this.messages.forEach(message => {
            const messageDiv = this.createMessageElement(message);
            messagesArea.appendChild(messageDiv);
        });
    }
    
    createMessageElement(message) {
        const div = document.createElement('div');
        div.className = `flex ${message.sender_type === 'admin' ? 'justify-end' : 'justify-start'}`;
        
        div.innerHTML = `
            <div class="max-w-xs lg:max-w-md">
                <div class="${message.sender_type === 'admin' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-900'} rounded-lg px-4 py-2">
                    <p class="text-sm">${this.escapeHtml(message.message)}</p>
                </div>
                <div class="text-xs text-gray-500 mt-1 ${message.sender_type === 'admin' ? 'text-right' : 'text-left'}">
                    ${message.sender_name || (message.sender_type === 'admin' ? 'Admin' : 'Pengunjung')} • 
                    ${new Date(message.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}
                </div>
            </div>
        `;
        
        return div;
    }
    
    async sendMessage(e) {
        e.preventDefault();
        
        const messageInput = document.getElementById('messageInput');
        const message = messageInput.value.trim();
        
        if (!message || !this.currentSessionId) return;
        
        messageInput.disabled = true;
        
        try {
            const response = await fetch('../api/chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'send_message',
                    session_id: this.currentSessionId,
                    sender_type: 'admin',
                    sender_name: this.currentUser.full_name,
                    sender_whatsapp: '',
                    message: message
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                messageInput.value = '';
                await this.loadMessages(this.currentSessionId);
            } else {
                this.showNotification('Gagal mengirim pesan: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Send message error:', error);
            this.showNotification('Terjadi kesalahan saat mengirim pesan', 'error');
        } finally {
            messageInput.disabled = false;
            messageInput.focus();
        }
    }
    
    async closeCurrentChat() {
        if (!this.currentSessionId) return;
        
        if (!confirm('Yakin ingin menutup chat ini?')) return;
        
        try {
            const response = await fetch('../api/chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'close_session',
                    session_id: this.currentSessionId
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification('Chat berhasil ditutup', 'success');
                this.currentSessionId = null;
                document.getElementById('chatArea').classList.add('hidden');
                document.getElementById('noChatSelected').classList.remove('hidden');
                await this.loadSessions();
            } else {
                this.showNotification('Gagal menutup chat: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Close chat error:', error);
            this.showNotification('Terjadi kesalahan saat menutup chat', 'error');
        }
    }
    
    async markCurrentChatAsRead() {
        if (!this.currentSessionId) return;
        
        try {
            const response = await fetch(`../api/chat.php?action=mark_read&session_id=${this.currentSessionId}`);
            const result = await response.json();
            
            if (result.success) {
                this.showNotification('Pesan ditandai sebagai dibaca', 'success');
                await this.loadSessions();
            }
        } catch (error) {
            console.error('Mark read error:', error);
        }
    }
    
    startRefreshInterval() {
        this.refreshInterval = setInterval(() => {
            this.refreshData();
        }, 5000); // Refresh every 5 seconds
    }
    
    stopRefreshInterval() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
    }
    
    async refreshData() {
        if (!this.isOnline) return;
        
        const refreshBtn = document.getElementById('refreshBtn');
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        try {
            await this.loadSessions();
            if (this.currentSessionId) {
                await this.loadMessages(this.currentSessionId);
            }
        } catch (error) {
            console.error('Refresh error:', error);
        } finally {
            refreshBtn.innerHTML = '<i class="fas fa-refresh"></i>';
        }
    }
    
    updateActiveChatsCount() {
        const count = this.sessions.filter(s => s.status === 'active').length;
        document.getElementById('activeChats').textContent = `${count} chat aktif`;
        
        // Update notification badge
        const unreadCount = this.sessions.reduce((total, session) => total + parseInt(session.unread_count || 0), 0);
        const badge = document.getElementById('notificationBadge');
        
        if (unreadCount > 0) {
            badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
    
    handleOnlineStatus(isOnline) {
        this.isOnline = isOnline;
        const statusEl = document.getElementById('connectionStatus');
        
        if (isOnline) {
            statusEl.innerHTML = '<span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>Online';
            this.startRefreshInterval();
            this.refreshData();
        } else {
            statusEl.innerHTML = '<span class="w-2 h-2 bg-red-400 rounded-full mr-2 offline-indicator"></span>Offline';
            this.stopRefreshInterval();
        }
    }
    
    checkOnlineStatus() {
        this.handleOnlineStatus(navigator.onLine);
    }
    
    // PWA and Push Notification methods
    async initServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('sw.js');
                this.serviceWorker = registration;
                console.log('Service Worker registered:', registration);
            } catch (error) {
                console.error('Service Worker registration failed:', error);
            }
        }
    }
    
    async requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            setTimeout(() => {
                document.getElementById('notificationRequest').classList.remove('hidden');
            }, 3000);
        }
    }
    
    async enableNotifications() {
        try {
            const permission = await Notification.requestPermission();
            this.notificationPermission = permission;
            
            if (permission === 'granted') {
                this.showNotification('Notifikasi berhasil diaktifkan!', 'success');
                
                // Subscribe to push notifications if available
                if (this.serviceWorker && 'pushManager' in window) {
                    await this.subscribeToPush();
                }
            }
        } catch (error) {
            console.error('Notification permission error:', error);
        }
        
        this.dismissNotificationRequest();
    }
    
    dismissNotificationRequest() {
        document.getElementById('notificationRequest').classList.add('hidden');
    }
    
    async subscribeToPush() {
        try {
            const subscription = await this.serviceWorker.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array('YOUR_VAPID_PUBLIC_KEY') // You'll need to generate VAPID keys
            });
            
            // Send subscription to server
            await fetch('../api/push-subscription.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    subscription: subscription,
                    admin_id: this.currentUser.id
                })
            });
            
            console.log('Push subscription successful');
        } catch (error) {
            console.error('Push subscription failed:', error);
        }
    }
    
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');
        
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);
        
        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
    
    showBrowserNotification(title, body, icon = null) {
        if (this.notificationPermission === 'granted' && document.hidden) {
            new Notification(title, {
                body: body,
                icon: icon || '../assets/images/logo.svg',
                tag: 'live-chat',
                requireInteraction: true
            });
        }
    }
    
    // Utility methods
    scrollToBottom() {
        const messagesArea = document.getElementById('messagesArea');
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }
    
    timeAgo(dateString) {
        const now = new Date();
        const date = new Date(dateString);
        const diffInSeconds = Math.floor((now - date) / 1000);
        
        if (diffInSeconds < 60) return 'Baru saja';
        if (diffInSeconds < 3600) return `${Math.floor(diffInSeconds / 60)} menit lalu`;
        if (diffInSeconds < 86400) return `${Math.floor(diffInSeconds / 3600)} jam lalu`;
        return `${Math.floor(diffInSeconds / 86400)} hari lalu`;
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/\n/g, '<br>');
    }
    
    showNotification(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform translate-x-full transition-transform duration-300`;
        
        switch (type) {
            case 'success':
                toast.classList.add('bg-green-500', 'text-white');
                break;
            case 'error':
                toast.classList.add('bg-red-500', 'text-white');
                break;
            case 'warning':
                toast.classList.add('bg-yellow-500', 'text-gray-900');
                break;
            default:
                toast.classList.add('bg-blue-500', 'text-white');
        }
        
        toast.innerHTML = `
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="font-medium">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 hover:opacity-75">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);
        
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 300);
        }, 5000);
    }
}

// Initialize app when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.chatApp = new LiveChatApp();
});

// Install PWA prompt
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredPrompt = e;
    
    // Show install button or notification
    console.log('PWA install prompt available');
});
