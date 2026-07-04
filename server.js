const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Priority: Static files
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
        res.json({ success: true, id: order.id });
    } catch (err) {
        res.status(500).json({ success: false });
    }
};

app.post('/api/save-data', saveOrder);
app.post('/api/orders', saveOrder);

app.get('/api/get-data', (req, res) => {
    const authHeader = req.headers.authorization;
    if (authHeader === 'authenticated_session_token') {
        res.json(orders);
    } else {
        res.status(403).json({ success: false });
    }
});

app.post('/api/update-status', (req, res) => {
    const authHeader = req.headers.authorization;
    if (authHeader !== 'authenticated_session_token') {
        return res.status(403).json({ success: false });
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

// Route for all HTML files to ensure they load correctly
app.get('/:page.html', (req, res) => {
    res.sendFile(path.join(__dirname, req.params.page + '.html'), (err) => {
        if (err) {
            res.status(404).send('Page not found');
        }
    });
});

// Default route
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index_ar.html'));
});

// Handle 404
app.use((req, res) => {
    res.status(404).send('Not Found');
});

app.listen(PORT, () => {
    console.log('Server is running on port ' + PORT);
});
