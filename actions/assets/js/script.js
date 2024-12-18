const tabs = document.querySelectorAll('.tab-button');
        const contents = document.querySelectorAll('.content');

        function switchTab(tabId){
            tabs.forEach(tab => tab.classList.remove('active'));
            contents.forEach(content => content.classList.remove('active'));
            document.getElementById(tabId).classList.add('active');
            document.querySelector(`.tab-button[onclick="switchTab('${tabId}')"]`).classList.add('active');
        }

        // Open Modal
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "flex";
        }
    
        // Close Modal
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }
        // Close modal on outside click
        window.onclick = function(event) {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                if (event.target === modal) {
                    modal.style.display = "none";
                }
            });
        }

    function loadContent(tabId, fileName) {
        fetch(fileName)
            .then(response => response.text())
            .then(data => {
                document.getElementById(tabId).innerHTML = data;
            });
    }

