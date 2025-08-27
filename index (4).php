<!DOCTYPE html>
<html lang="hi">
<head>
  <meta charset="UTF-8">
  <title>📒 उधार हिसाब Calculator</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {font-family: Arial, sans-serif; background:#f4f4f9; padding:15px; margin:0;}
    h2 {text-align:center; color:#333;}

    /* 🔍 Search Box */
    .search-section {text-align:center; margin:15px 0;}
    .search-section input {
      width:90%; max-width:400px;
      padding:12px;
      border:2px solid #1565c0;
      border-radius:8px;
      font-size:16px;
    }
    .search-section button {
      margin-top:10px;
      padding:10px 20px;
      border:none;
      border-radius:8px;
      background:#1565c0;
      color:#fff;
      font-size:16px;
      cursor:pointer;
    }
    .search-section button:hover {background:#0d47a1;}

    /* Form box */
    .form-box {background:#fff; padding:15px; border-radius:12px; box-shadow:0 2px 6px rgba(0,0,0,0.2); margin-bottom:15px;}
    label {display:block; margin-top:10px; font-weight:bold; color:#444;}
    input, select {width:100%; padding:10px; margin-top:5px; border:1px solid #ccc; border-radius:8px;}
    button.save {margin-top:15px; padding:12px; border:none; border-radius:8px; background:#4CAF50; color:#fff; font-size:15px; cursor:pointer; width:100%;}
    button.save:hover {background:#43a047;}
    button.download {margin-top:10px; padding:12px; border:none; border-radius:8px; background:#1565c0; color:#fff; font-size:15px; cursor:pointer; width:100%;}
    button.download:hover {background:#0d47a1;}

    /* Table */
    table {width:100%; border-collapse:collapse; margin-top:15px; font-size:14px;}
    th, td {border:1px solid #ddd; padding:8px; text-align:center;}
    th {background:#4CAF50; color:white;}

    /* Summary */
    .summary {display:flex; justify-content:space-between; margin-top:15px; gap:10px; flex-wrap:wrap;}
    .box {flex:1; min-width:100px; padding:10px; border-radius:10px; color:#fff; text-align:center; font-weight:bold;}
    .green {background:#2e7d32;}
    .red {background:#c62828;}
    .blue {background:#1565c0;}

    @media (max-width:600px) {
      table {font-size:12px;}
      th, td {padding:6px;}
    }
  </style>
</head>
<body>

  <h2>📒 उधार / हिसाब Calculator</h2>

  <!-- 🔍 Search Section -->
  <div class="search-section">
    <input type="text" id="search" placeholder="नाम या मोबाइल से खोजें...">
    <br>
    <button onclick="searchRecord()">🔍 खोजें</button>
  </div>

  <!-- Form -->
  <div class="form-box">
    <label>नाम:</label>
    <input type="text" id="name" placeholder="नाम लिखें">

    <label>मोबाइल नंबर:</label>
    <input type="text" id="mobile" placeholder="मोबाइल नंबर लिखें">

    <label>हिसाब का प्रकार:</label>
    <select id="type">
      <option value="Diya">किसको दिया</option>
      <option value="Liya">किससे लिया</option>
    </select>

    <label>राशि (₹):</label>
    <input type="number" id="amount" placeholder="राशि डालें">

    <button class="save" onclick="addRecord()">सेव करें</button>
    <button class="download" onclick="downloadCSV()">⬇️ Download CSV</button>
  </div>

  <!-- Summary -->
  <div class="summary">
    <div class="box green" id="totalDiya">कुल दिया: ₹0</div>
    <div class="box red" id="totalLiya">कुल लिया: ₹0</div>
    <div class="box blue" id="netBalance">नेट बैलेंस: ₹0</div>
  </div>

  <!-- Table -->
  <table id="historyTable">
    <thead>
      <tr>
        <th>नाम</th>
        <th>मोबाइल</th>
        <th>प्रकार</th>
        <th>राशि (₹)</th>
        <th>तारीख व समय</th>
      </tr>
    </thead>
    <tbody></tbody>
  </table>

<script>
  let records = JSON.parse(localStorage.getItem("hisabRecords")) || [];

  function addRecord() {
    const name = document.getElementById("name").value.trim();
    const mobile = document.getElementById("mobile").value.trim();
    const type = document.getElementById("type").value;
    const amount = document.getElementById("amount").value;

    if (name === "" || mobile === "" || amount === "") {
      alert("कृपया सभी जानकारी भरें!");
      return;
    }

    const now = new Date();
    const dateTime = now.toLocaleDateString() + " " + now.toLocaleTimeString();

    const record = {name, mobile, type, amount:parseFloat(amount), dateTime};
    records.push(record);

    localStorage.setItem("hisabRecords", JSON.stringify(records));
    renderTable(records);

    document.getElementById("name").value = "";
    document.getElementById("mobile").value = "";
    document.getElementById("amount").value = "";
  }

  function renderTable(data) {
    const tbody = document.querySelector("#historyTable tbody");
    tbody.innerHTML = "";
    let totalDiya = 0, totalLiya = 0;

    data.forEach(r => {
      if (r.type === "Diya") totalDiya += r.amount;
      else totalLiya += r.amount;

      const row = `<tr>
        <td>${r.name}</td>
        <td>${r.mobile}</td>
        <td>${r.type}</td>
        <td>${r.amount}</td>
        <td>${r.dateTime}</td>
      </tr>`;
      tbody.innerHTML += row;
    });

    document.getElementById("totalDiya").innerText = "कुल दिया: ₹" + totalDiya;
    document.getElementById("totalLiya").innerText = "कुल लिया: ₹" + totalLiya;

    // नेट बैलेंस प्लस/माइनस के साथ
    let net = totalDiya - totalLiya;
    let netBox = document.getElementById("netBalance");
    if (net > 0) {
      netBox.innerText = "नेट बैलेंस: +₹" + net;
      netBox.style.background = "#2e7d32"; // हरा
    } else if (net < 0) {
      netBox.innerText = "नेट बैलेंस: -₹" + Math.abs(net);
      netBox.style.background = "#c62828"; // लाल
    } else {
      netBox.innerText = "नेट बैलेंस: ₹0";
      netBox.style.background = "#1565c0"; // नीला
    }
  }

  function searchRecord() {
    const q = document.getElementById("search").value.toLowerCase();
    if (q === "") {
      renderTable(records);
      return;
    }
    const filtered = records.filter(r => r.name.toLowerCase().includes(q) || r.mobile.includes(q));
    renderTable(filtered);

    // व्यक्ति का कुल हिसाब Popup में दिखाओ
    let diya = 0, liya = 0;
    filtered.forEach(r => {
      if (r.type === "Diya") diya += r.amount;
      else liya += r.amount;
    });
    if (filtered.length > 0) {
      let net = diya - liya;
      let msg = `${filtered[0].name} (${filtered[0].mobile}) का कुल हिसाब:\nदिया: ₹${diya} | लिया: ₹${liya} | `;
      if (net > 0) msg += `बाक़ी: +₹${net}`;
      else if (net < 0) msg += `बाक़ी: -₹${Math.abs(net)}`;
      else msg += `बाक़ी: ₹0`;
      alert(msg);
    } else {
      alert("कोई रिकॉर्ड नहीं मिला!");
    }
  }

  function downloadCSV() {
    let csv = "नाम,मोबाइल,प्रकार,राशि,तारीख व समय\n";
    records.forEach(r => {
      csv += `${r.name},${r.mobile},${r.type},${r.amount},${r.dateTime}\n`;
    });
    const blob = new Blob([csv], {type: 'text/csv'});
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.setAttribute("href", url);
    a.setAttribute("download", "hisab_records.csv");
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }

  renderTable(records);
</script>

</body>
</html>