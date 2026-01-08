// chat/channel.js
let currentChannel = null;
export function getChatChannelName(authUserId, otherUserId) {
    const ids = [authUserId, otherUserId].sort((a, b) => a - b);
    return `chat.${ids[0]}.${ids[1]}`;
}

export function subscribeToChat(authUserId, otherUserId, onMessageReceived) {
    const channelName = getChatChannelName(authUserId, otherUserId);

    // Leave old channel
    if (currentChannel) {
        Echo.leave(currentChannel.name);
        currentChannel = null;
    }

    currentChannel = Echo.private(channelName);

    currentChannel
        .listen('.MessageSent', (e) => {
            onMessageReceived(e.message, e.user);
        })
        .subscribed(() => {
            console.log('Subscribed to:', channelName);
        })
        .error((err) => {
            console.error('Channel error:', err);
        });

    return currentChannel;
}

export function unsubscribeCurrent() {
    if (currentChannel) {
        Echo.leave(currentChannel.name);
        currentChannel = null;
    }
}
