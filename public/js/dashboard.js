// Function to update the circular progress bars
function updateCircleProgress(elementId, value) {
    const circle = document.getElementById(elementId); // Get the SVG circle by ID
    const radius = circle.r.baseVal.value; // Get the radius of the circle
    const circumference = 2 * Math.PI * radius; // Calculate the circumference

    // Calculate the stroke offset based on the value
    const offset = circumference - (value / 100) * circumference;

    // Set the stroke-dasharray and stroke-dashoffset for the circle
    circle.style.strokeDasharray = `${circumference} ${circumference}`;
    circle.style.strokeDashoffset = offset;
}

// Function to update usage values and circular progress bars
function updateUsage(cpu, ram, input, output) {
    // Update Circular Progress Bars
    updateCircleProgress('cpu-usage', cpu);
    updateCircleProgress('ram-usage', ram);
    updateCircleProgress('bandwidth-input', input);
    updateCircleProgress('bandwidth-output', output);

    // Update the text inside the labels
    document.querySelector('#cpu-usage-value').innerText = `${cpu}%`;
    document.querySelector('#ram-usage-value').innerText = `${ram}%`;
    document.querySelector('#bandwidth-input-value').innerText = `${input}%`;
    document.querySelector('#bandwidth-output-value').innerText = `${output}%`;
}

// Function to fetch usage data from the server
function fetchServerUsage() {
    fetch('/api/get-server-usage.php')
        .then(response => response.json())
        .then(data => {
            // Example response: { cpu: 75, ram: 60, input: 50, output: 45 }
            updateUsage(data.cpu, data.ram, data.input, data.output);
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
