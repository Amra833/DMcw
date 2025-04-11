async function loginFunctionality() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    // validation......
    if (email === '' || password === '') {
        alert('Please fill all the fields.');
        return;
    }

    try {
        // try t o connect backend
        const response = await fetch('http://localhost:3306/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, password }),
        });


        const data = await response.json();


        if (response.ok) {
            alert(data.message);
            window.location.href = 'Buyer_page.html'; 
        } else {
            alert(data.message); 
        }
    } catch (error) {
        console.error('Error during login:', error);
        alert('An error occurred. Please try again later.');
    }
}
