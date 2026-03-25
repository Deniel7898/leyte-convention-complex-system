function updateDateTime() {
    const now = new Date();

    // Day, Month, Year
    const day = String(now.getDate()).padStart(2, '0');
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const month = monthNames[now.getMonth()];
    const year = now.getFullYear();

    // 12-hour format with AM/PM
    let hours = now.getHours();
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12; // convert to 12-hour
    hours = hours ? hours : 12; // the hour '0' should be '12'
    hours = String(hours).padStart(2, '0');

    document.getElementById('current-date').textContent = `${day} ${month} ${year} | ${hours}:${minutes}:${seconds} ${ampm}`;
}

// Update every second
setInterval(updateDateTime, 1000);
updateDateTime();
