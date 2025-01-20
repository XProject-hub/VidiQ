document.addEventListener('DOMContentLoaded', () => {
    const apiEndpoint = '/api/streams_api.php';
    const streamTableBody = document.getElementById('stream-table-body');
    const addStreamBtn = document.getElementById('add-stream-btn');

    // Fetch and display streams
    async function fetchStreams() {
        try {
            const response = await fetch(apiEndpoint);
            const data = await response.json();
            streamTableBody.innerHTML = '';
            data.forEach(stream => {
                streamTableBody.innerHTML += `
                    <tr>
                        <td>${stream.id}</td>
                        <td>${stream.name}</td>
                        <td>${stream.category}</td>
                        <td>
                            <button class="btn btn-edit" data-id="${stream.id}">Edit</button>
                            <button class="btn btn-delete" data-id="${stream.id}">Delete</button>
                        </td>
                    </tr>
                `;
            });
        } catch (err) {
            console.error('Error:', err);
        }
    }

    // Add a new stream
    addStreamBtn.addEventListener('click', async () => {
        const name = prompt('Enter stream name:');
        const category = prompt('Enter category:');
        if (name && category) {
            try {
                await fetch(apiEndpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, category }),
                });
                fetchStreams();
            } catch (err) {
                console.error('Error:', err);
            }
        }
    });

    // Handle edit and delete actions
    streamTableBody.addEventListener('click', async e => {
        if (e.target.classList.contains('btn-delete')) {
            const id = e.target.getAttribute('data-id');
            try {
                await fetch(apiEndpoint, {
                    method: 'DELETE',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id }),
                });
                fetchStreams();
            } catch (err) {
                console.error('Error:', err);
            }
        }

        if (e.target.classList.contains('btn-edit')) {
            const id = e.target.getAttribute('data-id');
            const name = prompt('Enter new stream name:');
            const category = prompt('Enter new category:');
            if (name && category) {
                try {
                    await fetch(apiEndpoint, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id, name, category }),
                    });
                    fetchStreams();
                } catch (err) {
                    console.error('Error:', err);
                }
            }
        }
    });

    // Initial fetch
    fetchStreams();
});
