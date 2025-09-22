// Toast Notification System
class ToastManager {
    constructor() {
        this.container = null;
        this.init();
    }

    init() {
        // Create toast container
        this.container = document.createElement('div');
        this.container.id = 'toast-container';
        this.container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
        `;
        document.body.appendChild(this.container);
    }

    show(message, type = 'info', duration = 4000) {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        // Toast styles
        const baseStyles = `
            padding: 16px 20px;
            border-radius: 12px;
            color: white;
            font-weight: 500;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            transform: translateX(100%);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            min-width: 300px;
        `;

        let typeStyles = '';
        switch (type) {
            case 'success':
                typeStyles = 'background: linear-gradient(135deg, #10b981 0%, #059669 100%);';
                break;
            case 'error':
                typeStyles = 'background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);';
                break;
            case 'warning':
                typeStyles = 'background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);';
                break;
            case 'info':
            default:
                typeStyles = 'background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);';
                break;
        }

        toast.style.cssText = baseStyles + typeStyles;
        
        // Add icon based on type
        let icon = '';
        switch (type) {
            case 'success': icon = '✅'; break;
            case 'error': icon = '❌'; break;
            case 'warning': icon = '⚠️'; break;
            case 'info': icon = 'ℹ️'; break;
        }

        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">${icon}</span>
                <span>${message}</span>
                <span style="margin-left: auto; font-size: 20px; cursor: pointer; opacity: 0.7;" onclick="this.parentElement.parentElement.remove()">×</span>
            </div>
        `;

        // Add progress bar
        const progressBar = document.createElement('div');
        progressBar.style.cssText = `
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
            width: 100%;
            transform-origin: left;
            animation: toast-progress ${duration}ms linear forwards;
        `;
        toast.appendChild(progressBar);

        // Add CSS animation for progress bar
        if (!document.getElementById('toast-styles')) {
            const style = document.createElement('style');
            style.id = 'toast-styles';
            style.textContent = `
                @keyframes toast-progress {
                    from { transform: scaleX(1); }
                    to { transform: scaleX(0); }
                }
                .toast:hover .toast-progress {
                    animation-play-state: paused;
                }
            `;
            document.head.appendChild(style);
        }

        this.container.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 10);

        // Auto remove
        setTimeout(() => {
            this.remove(toast);
        }, duration);

        // Click to remove
        toast.addEventListener('click', () => {
            this.remove(toast);
        });

        return toast;
    }

    remove(toast) {
        if (toast && toast.parentElement) {
            toast.style.transform = 'translateX(100%)';
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.parentElement.removeChild(toast);
                }
            }, 300);
        }
    }

    success(message, duration = 4000) {
        return this.show(message, 'success', duration);
    }

    error(message, duration = 5000) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration = 4500) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration = 4000) {
        return this.show(message, 'info', duration);
    }
}

// Confirmation Dialog System
class ConfirmDialog {
    static show(message, title = 'تأكيد الإجراء', options = {}) {
        return new Promise((resolve) => {
            // Create overlay
            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(5px);
                z-index: 10001;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                transition: opacity 0.3s ease;
            `;

            // Create dialog
            const dialog = document.createElement('div');
            dialog.style.cssText = `
                background: white;
                border-radius: 16px;
                padding: 30px;
                max-width: 450px;
                width: 90%;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
                transform: scale(0.9);
                transition: transform 0.3s ease;
            `;

            const confirmText = options.confirmText || 'تأكيد';
            const cancelText = options.cancelText || 'إلغاء';
            const type = options.type || 'warning';

            let icon = '';
            let iconColor = '';
            switch (type) {
                case 'danger':
                    icon = '🗑️';
                    iconColor = '#ef4444';
                    break;
                case 'warning':
                    icon = '⚠️';
                    iconColor = '#f59e0b';
                    break;
                case 'info':
                    icon = 'ℹ️';
                    iconColor = '#3b82f6';
                    break;
            }

            dialog.innerHTML = `
                <div style="text-align: center; margin-bottom: 25px;">
                    <div style="font-size: 48px; margin-bottom: 15px;">${icon}</div>
                    <h3 style="margin: 0 0 10px 0; color: #1f2937; font-size: 20px;">${title}</h3>
                    <p style="margin: 0; color: #6b7280; line-height: 1.5;">${message}</p>
                </div>
                <div style="display: flex; gap: 12px; justify-content: center;">
                    <button id="cancelBtn" style="
                        padding: 12px 24px;
                        border: 2px solid #e5e7eb;
                        background: white;
                        color: #374151;
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.2s ease;
                        min-width: 100px;
                    ">${cancelText}</button>
                    <button id="confirmBtn" style="
                        padding: 12px 24px;
                        border: none;
                        background: ${iconColor};
                        color: white;
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.2s ease;
                        min-width: 100px;
                    ">${confirmText}</button>
                </div>
            `;

            overlay.appendChild(dialog);
            document.body.appendChild(overlay);

            // Animate in
            setTimeout(() => {
                overlay.style.opacity = '1';
                dialog.style.transform = 'scale(1)';
            }, 10);

            // Event listeners
            const confirmBtn = dialog.querySelector('#confirmBtn');
            const cancelBtn = dialog.querySelector('#cancelBtn');

            const cleanup = () => {
                overlay.style.opacity = '0';
                dialog.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    if (overlay.parentElement) {
                        overlay.parentElement.removeChild(overlay);
                    }
                }, 300);
            };

            confirmBtn.addEventListener('click', () => {
                cleanup();
                resolve(true);
            });

            cancelBtn.addEventListener('click', () => {
                cleanup();
                resolve(false);
            });

            // Close on overlay click
            overlay.addEventListener('click', (e) => {
                if (e.target === overlay) {
                    cleanup();
                    resolve(false);
                }
            });

            // Hover effects
            confirmBtn.addEventListener('mouseenter', () => {
                confirmBtn.style.transform = 'translateY(-2px)';
                confirmBtn.style.boxShadow = `0 8px 25px ${iconColor}40`;
            });

            confirmBtn.addEventListener('mouseleave', () => {
                confirmBtn.style.transform = 'translateY(0)';
                confirmBtn.style.boxShadow = 'none';
            });

            cancelBtn.addEventListener('mouseenter', () => {
                cancelBtn.style.transform = 'translateY(-2px)';
                cancelBtn.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.1)';
                cancelBtn.style.borderColor = '#d1d5db';
            });

            cancelBtn.addEventListener('mouseleave', () => {
                cancelBtn.style.transform = 'translateY(0)';
                cancelBtn.style.boxShadow = 'none';
                cancelBtn.style.borderColor = '#e5e7eb';
            });
        });
    }
}

// Initialize global instances
window.toast = new ToastManager();
window.confirmDialog = ConfirmDialog;

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ToastManager, ConfirmDialog };
}
