document.addEventListener("DOMContentLoaded", function () {
    fetch("fetch_owner_houses.php")
        .then(response => response.json())
        .then(data => {
            console.log("Fetched Data:", data); // Debugging

            if (data.error) {
                alert(data.error);
                return;
            }
            
            const houseList = document.getElementById("house-list");
            houseList.innerHTML = ""; // Clear any existing content

            if (data.length === 0) {
                houseList.innerHTML = "<tr><td colspan='7'>No houses found</td></tr>";
                return;
            }

            data.forEach(house => {
                const row = document.createElement("tr");

                row.innerHTML = `
                    <td>${house.title}</td>
                    <td>${house.location}</td>
                    <td>₹${house.price}</td>
                    <td>${house.house_type}</td>
                    <td>${house.landmark}</td>
                    <td><img src="uploads/${house.image}" alt="House Image" width="100"></td>
                    <td><button onclick="deleteHouse(${house.id})">Delete</button></td>
                `;

                houseList.appendChild(row);
            });
        })
        .catch(error => console.error("Error fetching houses:", error));
});
