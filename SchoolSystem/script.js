document.getElementById("studentForm").addEventListener("submit", function(event) {
    event.preventDefault();
    let formData = new FormData(this);
    fetch("insert.php", { method: "POST", body: formData })
        .then(response => response.text())
        .then(data => { console.log(data); location.reload(); });
});

function loadStudents() {
    fetch("retrieve.php")
        .then(response => response.json())
        .then(data => {
            let list = document.getElementById("studentList");
            list.innerHTML = "";
            data.forEach(student => {
                list.innerHTML += `<p>${student.name}, ${student.age}, ${student.class}, ${student.parent_contact}</p>`;
            });
        });
}
loadStudents();
