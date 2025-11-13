<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lessons List</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        table { border-collapse: collapse; width: 100%; margin-top: 2rem; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 8px; text-align: left; }
        input[type=text], textarea { width: 100%; }
        button { cursor: pointer; margin-right: 5px; }
    </style>
</head>
<body>
    <h1>Lesson Materials</h1>

    <!-- Upload Form -->
    <h2>Add New Lesson</h2>
    <form id="createLessonForm" enctype="multipart/form-data">
        @csrf
        <div>
            <label>Title:</label><br>
            <input type="text" name="title" required>
        </div>

        <div style="margin-top: 1rem;">
            <label>Description:</label><br>
            <textarea name="description" rows="3"></textarea>
        </div>

        <div style="margin-top: 1rem;">
            <label>Upload File:</label><br>
            <input type="file" name="file" accept=".pdf,.docx,.pptx,.txt,.jpg,.png">
        </div>

        <button type="submit" style="margin-top: 1rem;">Upload Lesson</button>
    </form>

    <!-- Lesson List -->
    <h2>Lesson List</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Description</th>
                <th>File</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="lessonTableBody">
            <!-- Filled by JS -->
        </tbody>
    </table>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Load lessons
        async function loadLessons() {
            const response = await fetch('/api/lessons');
            const lessons = await response.json();
            const tableBody = document.getElementById('lessonTableBody');
            tableBody.innerHTML = '';

            lessons.forEach((lesson, index) => {  // add index
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${index + 1}</td>  <!-- sequential row number -->
        <td><input type="text" id="title-${lesson.id}" value="${lesson.title}"></td>
        <td><textarea id="desc-${lesson.id}">${lesson.description || ''}</textarea></td>
        <td>
            <input type="file" id="file-${lesson.id}">
            ${lesson.file_path ? `<a href="/storage/${lesson.file_path}" target="_blank">View File</a>` : 'No file'}
        </td>
        <td>
            <button onclick="updateLesson(${lesson.id})">Edit</button>
            <button onclick="deleteLesson(${lesson.id})">Delete</button>
        </td>
    `;
    tableBody.appendChild(row);
});

        }

        // Create lesson
        document.getElementById('createLessonForm').addEventListener('submit', async function(e){
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('_token', csrfToken);

            try {
                const response = await fetch('/api/lessons', { method: 'POST', body: formData });
                const data = await response.json();
                if (!data.success) throw new Error(data.message || 'Unknown error');
                alert('Lesson created successfully!');
                this.reset();
                loadLessons();
            } catch(err){
                console.error(err);
                alert('Unexpected error: ' + err.message);
            }
        });

        // Update lesson
        async function updateLesson(id) {
            const formData = new FormData();
            formData.append('title', document.getElementById(`title-${id}`).value);
            formData.append('description', document.getElementById(`desc-${id}`).value);
            formData.append('_method', 'PUT');
            formData.append('_token', csrfToken);

            const fileInput = document.getElementById(`file-${id}`);
            if (fileInput.files[0]) formData.append('file', fileInput.files[0]);

            try {
                const response = await fetch(`/api/lessons/${id}`, { method: 'POST', body: formData });
                const data = await response.json();
                if (!data.success) throw new Error(data.message || 'Unknown error');
                alert(data.message);
                loadLessons();
            } catch(err){
                console.error(err);
                alert('Unexpected error: ' + err.message);
            }
        }

        // Delete lesson
        async function deleteLesson(id) {
            if (!confirm('Are you sure you want to delete this lesson?')) return;

            const formData = new FormData();
            formData.append('_token', csrfToken);

            try {
                const response = await fetch(`/api/lessons/${id}/delete`, { method: 'POST', body: formData });
                const data = await response.json();
                if (!data.success) throw new Error(data.message || 'Unknown error');
                alert(data.message);
                loadLessons();
            } catch(err){
                console.error(err);
                alert('Unexpected error: ' + err.message);
            }
        }

        // Load lessons on page load
        loadLessons();
    </script>
</body>
</html>
