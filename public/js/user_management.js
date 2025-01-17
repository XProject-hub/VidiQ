document.addEventListener('DOMContentLoaded', () => {
    const userTable = document.getElementById('user-table').querySelector('tbody');
    const addUserBtn = document.getElementById('add-user-btn');

    // Fetch users and populate the table
    function fetchUsers() {
        fetch('/api/user_api.php')
            .then(response => response.json())
            .then(users => {
                userTable.innerHTML = '';
                users.forEach(user => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                        <td>
                            <button onclick="editUser(${user.id})">Edit</button>
                            <button onclick="deleteUser(${user.id})">Delete</button>
                        </td>
                    `;
                    userTable.appendChild(row);
                });
            });
    }

    // Add user
    addUserBtn.addEventListener('click', () => {
        const username = prompt('Enter username:');
        const email = prompt('Enter email:');
        const password = prompt('Enter password:');
        fetch('/api/user_api.php', {
            method: 'POST',
            body: JSON.stringify({ username, email, password }),
            headers: { 'Content-Type': 'application/json' }
        }).then(fetchUsers);
    });

    // Edit user
    window.editUser = id => {
        const email = prompt('Enter new email:');
        const password = prompt('Enter new password:');
        fetch('/api/user_api.php', {
            method: 'PUT',
            body: `id=${id}&email=${email}&password=${password}`,
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
        }).then(fetchUsers);
    };

    // Delete user
    window.deleteUser = id => {
        if (confirm('Are you sure you want to delete this user?')) {
            fetch('/api/user_api.php', {
                method: 'DELETE',
                body: `id=${id}`,
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
            }).then(fetchUsers);
        }
    };

    // Initial load
    fetchUsers();
});
