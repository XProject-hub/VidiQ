// Fetch and display users
function fetchUsers() {
    fetch(apiEndpoint)
        .then(response => response.json())
        .then(data => {
            userTableBody.innerHTML = '';
            data.data.forEach(user => {
                userTableBody.innerHTML += `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.email}</td>
                        <td>${user.role}</td>
                        <td>
                            ${user.role !== 'Admin' ? `
                            <button class="btn btn-edit" data-id="${user.id}" data-username="${user.username}" data-email="${user.email}" data-role="${user.role}">Edit</button>
                            <button class="btn btn-delete" data-id="${user.id}">Delete</button>` : '<span>No actions allowed</span>'}
                        </td>
                    </tr>
                `;
            });

            // Toggle "Add User" button visibility based on role
            const currentUserRole = data.currentUserRole || 'Viewer';
            addUserBtn.style.display = currentUserRole === 'Admin' ? 'inline-block' : 'none';
        })
        .catch(err => console.error('Error:', err));
}

// Show Modal for Add or Edit
function showModal(edit = false, user = {}) {
    modalTitle.innerText = edit ? 'Edit User' : 'Add User';
    userForm.reset();
    userModal.style.display = 'block';

    if (edit) {
        document.getElementById('user-id').value = user.id;
        document.getElementById('username').value = user.username;
        document.getElementById('email').value = user.email;
        document.getElementById('role').value = user.role;
    }
}

// Hide Modal
function hideModal() {
    userModal.style.display = 'none';
}

// Submit Form for Add/Edit User
userForm.addEventListener('submit', e => {
    e.preventDefault();

    const formData = new FormData(userForm);
    const id = formData.get('id');
    const method = id ? 'PUT' : 'POST';

    fetch(apiEndpoint, {
        method: method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(Object.fromEntries(formData.entries())),
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                fetchUsers();
                hideModal();
            } else {
                console.error('Error:', data.message);
            }
        })
        .catch(err => console.error('Error:', err));
});

// Add User Button Click
addUserBtn.addEventListener('click', () => showModal(false));

// Close Modal Button
closeModal.addEventListener('click', hideModal);

// Handle Edit and Delete Buttons in Table
userTableBody.addEventListener('click', e => {
    if (e.target.classList.contains('btn-delete')) {
        const id = e.target.getAttribute('data-id');

        if (confirm('Are you sure you want to delete this user?')) {
            fetch(apiEndpoint, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        fetchUsers();
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(err => console.error('Error:', err));
        }
    }

    if (e.target.classList.contains('btn-edit')) {
        const id = e.target.getAttribute('data-id');
        const username = e.target.getAttribute('data-username');
        const email = e.target.getAttribute('data-email');
        const role = e.target.getAttribute('data-role');
        showModal(true, { id, username, email, role });
    }
});

// Initial Fetch
fetchUsers();
