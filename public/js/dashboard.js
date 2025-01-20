function fetchServerUsage() {
    fetch('/api/get-server-usage.php')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            updateServerUsage(data.cpu, data.ram, data.input, data.output);
        })
        .catch(error => {
            console.error('Error fetching server usage:', error);
            alert('There was an issue fetching the server usage. Please try again later.');
        });
}

function fetchStats() {
    fetch('/api/dashboard_stats.php')
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            totalUsers.textContent = data.totalUsers || 0;
            activeStreams.textContent = data.activeStreams || 0;

            if (serverHealthCanvas) {
                const ctx = serverHealthCanvas.getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['CPU', 'RAM', 'Disk'],
                        datasets: [{
                            data: [data.cpuUsage, data.ramUsage, data.diskUsage],
                            backgroundColor: ['#00ffff', '#008b8b', '#2c2c2c']
                        }]
                    },
                    options: { responsive: true }
                });
            }

            if (recentLogs) {
                recentLogs.innerHTML = '';
                data.recentLogs.forEach(log => {
                    const li = document.createElement('li');
                    li.textContent = log;
                    recentLogs.appendChild(li);
                });
            }
        })
        .catch(err => {
            console.error('Error fetching stats:', err);
            alert('There was an issue fetching the dashboard statistics. Please try again later.');
        });
}
