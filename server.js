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

// Universal API to save data (supports both /api/save-data and /api/orders)
const saveOrder = (req, res) => {
    try {
        const data = req.body;
        const order = {
            id: Date.now(),
            timestamp: new Date().toLocaleString('ar-AE'),
            status: data.status || 'جديد',
            name: data.name || data.full_name || 'غير معروف',
            email: data.email || '',
            phone: data.phone || '',
            country: data.country || data.emirate || '',
            amount: data.amount || '5.00',
            details: data.details || data
        };
        orders.push(order);
        console.log('New order saved:', order.id);
        res.json({ success: true, id: order.id });
    } catch (err) {
        console.error('Error saving order:', err);
        res.status(500).json({ success: false });
    }
};

app.post('/api/save-data', saveOrder);
app.post('/api/orders', saveOrder);

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
    const order = orders.find(o => o.id === parseInt(id) || o.id === id);
    if (order) {
        order.status = status;
        res.json({ success: true });
    } else {
        res.status(404).json({ success: false });
    }
});

// Serve HTML files
app.get('/:page.html', (req, res) => {
    res.sendFile(path.join(__dirname, req.params.page + '.html'));
});

app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index_ar.html'));
});

// Error handling to prevent crash
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).send('Something broke!');
});

app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});
