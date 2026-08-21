<!DOCTYPE html>
<html>
<head>
    <title>Test Kalender API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #7c3aed;
        }
        .info {
            background: #f3e8ff;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .data {
            background: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }
        button {
            background: #7c3aed;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            margin: 5px;
        }
        button:hover {
            background: #6d28d9;
        }
        .error {
            background: #fee;
            color: #c00;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .success {
            background: #efe;
            color: #060;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test Kalender API</h1>
        
        <div class="info">
            <strong>API Endpoint:</strong> /api/kalender-data<br>
            <strong>Method:</strong> GET<br>
            <strong>Parameters:</strong> bulan, tahun
        </div>

        <div>
            <button onclick="testAPI(12, 2025)">Test Desember 2025</button>
            <button onclick="testAPI(11, 2025)">Test November 2025</button>
            <button onclick="testAPI(1, 2026)">Test Januari 2026</button>
            <button onclick="testCurrentMonth()">Test Bulan Ini</button>
        </div>

        <div id="result"></div>
        <div id="data" class="data" style="display:none;"></div>
    </div>

    <script>
        async function testAPI(bulan, tahun) {
            const resultDiv = document.getElementById('result');
            const dataDiv = document.getElementById('data');
            
            resultDiv.innerHTML = '<div class="info">Loading...</div>';
            dataDiv.style.display = 'none';
            
            try {
                const response = await fetch(`/api/kalender-data?bulan=${bulan}&tahun=${tahun}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                resultDiv.innerHTML = `
                    <div class="success">
                        ✅ <strong>Success!</strong><br>
                        Bulan: ${bulan}/${tahun}<br>
                        Total Data: ${data.length} pengawasan
                    </div>
                `;
                
                dataDiv.textContent = JSON.stringify(data, null, 2);
                dataDiv.style.display = 'block';
                
                console.log('API Response:', data);
                
            } catch (error) {
                resultDiv.innerHTML = `
                    <div class="error">
                        ❌ <strong>Error!</strong><br>
                        ${error.message}
                    </div>
                `;
                console.error('API Error:', error);
            }
        }
        
        function testCurrentMonth() {
            const now = new Date();
            testAPI(now.getMonth() + 1, now.getFullYear());
        }
        
        // Auto test on load
        window.onload = () => {
            testCurrentMonth();
        };
    </script>
</body>
</html>

