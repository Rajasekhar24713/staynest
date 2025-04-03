document.getElementById("searchForm").addEventListener("submit", function (event) {
    event.preventDefault();

    let landmark = document.getElementById("landmark").value.trim();
    let distance = document.getElementById("distance").value.trim();

    if (!landmark) {
        alert("Please enter a landmark!");
        return;
    }

    // Construct query string
    let queryString = `search_house.php?landmark=${encodeURIComponent(landmark)}`;
    if (distance && !isNaN(distance) && parseFloat(distance) > 0) {
        queryString += `&distance=${encodeURIComponent(distance)}`;
    }

    fetch(queryString)
        .then(response => response.json())
        .then(data => {
            let resultsContainer = document.getElementById("houseResults");
            resultsContainer.innerHTML = "";

            if (data.length === 0) {
                resultsContainer.innerHTML = "<p>No houses found near the specified landmark within the given range.</p>";
            } else {
                data.forEach(house => {
                    // Get first image or use default
                    let imageUrl = house.images ? house.images.split(",")[0] : "default_house.jpg";

                    let houseCard = `
                        <div class="house-card" style="border: 1px solid #ddd; padding: 15px; margin: 10px;">
                            <h3>${house.title}</h3>
                            <p><strong>Location:</strong> ${house.location}</p>
                            <p><strong>Landmark:</strong> ${house.landmark}</p>
                            <p><strong>Distance:</strong> ${house.distance_from_landmark ? house.distance_from_landmark + " km" : "N/A"}</p>
                            <p><strong>Price:</strong> ₹${parseFloat(house.price).toLocaleString()}</p>
                            <p><strong>Phone:</strong> <a href="tel:${house.phone}">${house.phone}</a></p>
                            <img src="${imageUrl}" alt="House Image" style="width:100%; max-height:200px; object-fit:cover;">
                        </div>
                    `;
                    resultsContainer.innerHTML += houseCard;
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while fetching house details. Please try again.");
        });
});
