const { createServer } = require('http');
const Redis = require('ioredis');
const { Server } = require('socket.io');

// Create HTTP server
const server = createServer();

// Setup Socket.IO
const io = new Server(server, {
    cors: {
        origin: '*', // Adjust based on your frontend origin
    },
});

// Connect to Redis
const redis = new Redis();

// Subscribe to all Redis channels
redis.psubscribe('*', (err, count) => {
    if (err) {
        console.error('Failed to subscribe:', err.message);
    } else {
        console.log(`Subscribed to ${count} Redis channels.`);
    }
});

// Log messages received from Redis
redis.on('pmessage', (pattern, channel, message) => {
    console.log(`Message received on channel ${channel}: ${message}`);
    const data = JSON.parse(message);
    io.emit(channel, data.data);
});

// Handle client connections
io.on('connection', (socket) => {
    console.log(`Client connected: ${socket.id}`);

    socket.on('disconnect', () => {
        console.log(`Client disconnected: ${socket.id}`);
    });
});

// Start the server
server.listen(6001, () => {
    console.log('Socket.IO server running on port 6001');
});
