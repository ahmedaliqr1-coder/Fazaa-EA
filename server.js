const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(bodyParser.json());
app.use(express.static(path.join(__dirname, './')));

let orders = [];
const ADMIN_PASSWORD = 'Fazaa12345@@';

// API for Login
app.post('/api/login', (req, res) => {
    const { password } = req.body;
    if (password === ADMIN_PASSWORD) {
        res.json({ success: true, token: 'authenticated_session_token' });
    } else {
        res.status(401).json({ success: false, message: 'كلمة المرور غير صحيحة' });
    }
});

// API to save data
app.post('/api/save-data', (req, res) => {
    const data = req.body;
    data.id = Date.now();
    data.timestamp = new Date().toLocaleString('ar-AE');
    data.status = 'قيد الانتظار';
    orders.push(data);
    res.json({ success: true, id: data.id });
});

// API to get data (Protected)
app.get('/api/get-data', (req, res) => {
    const authHeader = req.headers.authorization;
    if (authHeader === 'authenticated_session_token') {
        res.json(orders);
    } else {
        res.status(403).json({ success: false, message: 'غير مصرح لك بالوصول' });
    }
});

// API to update status (Protected)
app.post('/api/update-status', (req, res) => {
    const authHeader = req.headers.authorization;
    if (authHeader !== 'authenticated_session_token') {
        return res.status(403).json({ success: false, message: 'غير مصرح لك بالوصول' });
    }
    const { id, status } = req.body;
    const order = orders.find(o => o.id === id);
    if (order) {
        order.status = status;
        res.json({ success: true });
    } else {
        res.status(404).json({ success: false });
    }
});

app.get('*', (req, res) => {
    res.sendFile(path.join(__dirname, 'index_ar.html'));
});

app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});
