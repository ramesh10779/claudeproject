require('dotenv').config();
const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const { createClient } = require('@supabase/supabase-js');
const Docker = require('dockerode');

const app = express();
const PORT = process.env.PORT || 3001;
const supabase = createClient(process.env.SUPABASE_URL, process.env.SUPABASE_SERVICE_KEY);
const docker = new Docker();
let simulators = {};

app.use(cors());
app.use(helmet());
app.use(express.json());

app.get('/health', (req, res) => res.json({ status: 'OK' }));

app.listen(PORT, () => console.log(`Backend listening on port ${PORT}`));
