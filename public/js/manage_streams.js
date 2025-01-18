document.addEventListener('DOMContentLoaded', () => {
    const apiEndpoint = '/api/streams_api.php';
    const streamTableBody = document.getElementById('stream-table-body');
    const addStreamBtn = document.getElementById('add-stream-btn');

    // Fetch and display streams
    function fetchStreams() {
        fetch(apiEndpoint)
            .then(response => response.json())
            .then(data => {
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
            })
            .catch(err => console.error('Error:', err));
    }

    // Add a new stream
    addStreamBtn.addEventListener('click', () => {
        const name = prompt('Enter stream name:');
        const category = prompt('Enter category:');
        fetch(apiEndpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, category }),
        }).then(() => fetchStreams());
    });

    // Handle edit and delete actions
    streamTableBody.addEventListener('click', e => {
        if (e.target.classList.contains('btn-delete')) {
            const id = e.target.getAttribute('data-id');
            fetch(apiEndpoint, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id }),
            }).then(() => fetchStreams());
        }

        if (e.target.classList.contains('btn-edit')) {
            const id = e.target.getAttribute('data-id');
            const name = prompt('Enter new stream name:');
            const category = prompt('Enter new category:');
            fetch(apiEndpoint, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, name, category }),
            }).then(() => fetchStreams());
        }
    });

    // Initial fetch
    fetchStreams();
});
