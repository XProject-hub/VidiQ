document.addEventListener('DOMContentLoaded', () => {
    const totalUsers = document.getElementById('total-users');
    const activeStreams = document.getElementById('active-streams');
    const serverHealthCanvas = document.getElementById('server-health');
    const recentLogs = document.getElementById('recent-logs');

    // Function to update the circular progress bars
    function updateCircleProgress(elementId, value, circumference) {
        const circle = document.getElementById(elementId);
        const offset = circumference - (value / 100) * circumference;
        circle.style.strokeDashoffset = offset;
    }

    function updateServerUsage(cpu, ram, input, output) {
        const cpuCircumference = 2 * Math.PI * 60; // r=60
        const ramCircumference = 2 * Math.PI * 45; // r=45
        const inputCircumference = 2 * Math.PI * 30; // r=30
        const outputCircumference = 2 * Math.PI * 15; // r=15

        // Update the progress circles
        updateCircleProgress('cpu-progress', cpu, cpuCircumference);
        updateCircleProgress('ram-progress', ram, ramCircumference);
        updateCircleProgress('input-progress', input, inputCircumference);
        updateCircleProgress('output-progress', output, outputCircumference);

        // Update the labels
        document.getElementById('cpu-label').innerText = `${cpu}%`;
        document.getElementById('ram-label').innerText = `${ram}%`;
        document.getElementById('input-label').innerText = `${input}%`;
        document.getElementById('output-label').innerText = `${output}%`;
    }

    // Function to fetch server usage data
    function fetchServerUsage() {
        fetch('/api/get-server-usage.php')
            .then(response => response.json())
            .then(data => {
                updateServerUsage(data.cpu, data.ram, data.input, data.output);
            })
            .catch(error => {
                console.error('Error fetching server usage:', error);
            });
    }

    // Function to fetch general dashboard stats
    function fetchStats() {
        fetch('/api/dashboard_stats.php')
            .then(response => response.json())
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
            .catch(err => console.error('Error fetching stats:', err));
    }

    // Initial fetch and periodic updates for all data
    fetchServerUsage();
    fetchStats();
    setInterval(() => {
        fetchServerUsage();
        fetchStats();
    }, 10000); // Update every 10 seconds
});
