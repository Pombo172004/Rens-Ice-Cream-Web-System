// Notification Bell Toggle and Fetch
window.addEventListener('DOMContentLoaded', function() {
  const notificationBell = document.getElementById('notificationBell');
  const notificationDropdown = document.getElementById('notificationDropdown');
  const notificationBadge = document.querySelector('.notification-badge');

  if (notificationBell && notificationDropdown) {
    notificationBell.addEventListener('click', function(e) {
      e.stopPropagation();
      // Fetch notifications when opening
      if (notificationDropdown.style.display !== 'block') {
        fetch('get_notifications.php')
          .then(res => res.json())
          .then(data => {
            if (data.status === 'success') {
              const notifs = data.notifications;
              let notifHtml = '';
              let unreadCount = 0;
              if (notifs.length === 0) {
                notifHtml = '<div style="padding: 16px; color: #888;">No new notifications</div>';
              } else {
                notifHtml = notifs.map(n => {
                  if (n.is_read == 0) unreadCount++;
                  return `<div style="padding: 12px 16px; border-bottom: 1px solid #eee; color: ${n.is_read == 0 ? '#222' : '#888'}; background: ${n.is_read == 0 ? '#f8f9fa' : '#fff'};">${n.message}<br><span style='font-size:12px;color:#aaa;'>${n.created_at}</span></div>`;
                }).join('');
              }
              notificationDropdown.innerHTML = `<div style='padding: 16px; border-bottom: 1px solid #eee; font-weight: bold;'>Notifications</div>${notifHtml}`;
              // Show badge if there are unread notifications
              if (unreadCount > 0) {
                notificationBadge.textContent = unreadCount;
                notificationBadge.style.display = 'block';
              } else {
                notificationBadge.style.display = 'none';
              }
            }
          });
      }
      notificationDropdown.style.display = notificationDropdown.style.display === 'block' ? 'none' : 'block';
    });
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (!notificationDropdown.contains(e.target) && e.target !== notificationBell) {
        notificationDropdown.style.display = 'none';
      }
    });
  }
}); 