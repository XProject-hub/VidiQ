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

// Function to fetch usage data from the server
function fetchServerUsage() {
    fetch('/api/get-server-usage.php')
        .then(response => response.json())
        .then(data => {
            // Example response: { cpu: 75, ram: 60, input: 50, output: 45 }
            updateServerUsage(data.cpu, data.ram, data.input, data.output);
        })
        .catch(error => {
            console.error('Error fetching server usage:', error);
        });
}

// Function to fetch server details (connections, live streams, etc.)
function fetchServerDetails() {
    fetch('/get-server-details.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.querySelector('#server-connections').innerText = data.connections;
                document.querySelector('#server-live-streams').innerText = data.liveStreams;

                // Optional: Use data to update other UI elements if needed
            } else {
                console.error('Error fetching server details:', data.message);
            }
        })
        .catch(error => console.error('Fetch error:', error));
}

// Fetch data on page load and periodically every 5 seconds
document.addEventListener('DOMContentLoaded', () => {
    fetchServerUsage();
    fetchServerDetails();

    setInterval(() => {
        fetchServerUsage();
        fetchServerDetails();
    }, 5000); // Update every 5 seconds
});
