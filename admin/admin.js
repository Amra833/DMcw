let navbar = document.querySelector('.navbar');

// Menu bar
document.querySelector('#menu-btn').onclick = () => {
    navbar.classList.toggle('active');
}

window.onscroll = () => {
    navbar.classList.remove('active');
}


// Function to fetch and display student and class counts
async function fetchDashboardData() {
    try {
      const response = await fetch('admin.php');
      
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
  
      const data = await response.json();
      
      // Update elements with the fetched data
      document.getElementById('currentDate').textContent = data.date;
     
      document.querySelector('.card2 .card-content div').textContent = data.studentCount;
      document.querySelector('.card3 .card-content div').textContent = data.classCount;
      document.getElementById('totalincome').textContent = `${data.totalIncome}`; // Update total income
      // Update welcome message with the username
      document.getElementById('greetingMessage').textContent = `Welcome, ${data.username}`; // Display the username
  
    } catch (error) {
      console.error('Error fetching data:', error);
    }
  }
  
  // Call the function to fetch and display data when the page loads
  fetchDashboardData();
  