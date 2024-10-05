// Function to handle editing a team member
function editMember(memberName) {
    // 1. Get the member's ID (you'll likely need to store this somewhere)
    let memberId = getMemberId(memberName); // Replace getMemberId with your logic to fetch the ID
  
    // 2. Redirect to the edit page, passing the ID as a parameter
    window.location.href = `edit.html?memberId=${memberId}`; // Replace 'edit.html' with your actual edit page
  }
  
  // Function to handle deleting a team member
  function deleteMember(memberName) {
    // 1. Get the member's ID (same as above)
    let memberId = getMemberId(memberName);
  
    // 2. Send a DELETE request to the backend API
    fetch(`/team/${memberId}`, {
      method: 'DELETE'
    })
      .then(response => {
        // 3. Handle the response from the server
        if (response.ok) {
          // If the deletion was successful, remove the member from the UI
          removeMemberFromUI(memberName); 
        } else {
          // Handle errors (e.g., display an error message)
          console.error('Error deleting member');
        }
      })
      .catch(error => {
        console.error('Error during DELETE request:', error);
      });
  }
  
  // Helper function to remove the member from the UI (after successful deletion)
  function removeMemberFromUI(memberName) {
    // Find the team member element in the DOM
    let memberElement = document.querySelector(`.team-member:has(h3:contains(${memberName}))`); 
  
    // Remove the member element
    memberElement.remove(); 
  }
  
  // Placeholder for getMemberId (you'll need to implement this based on your data storage)
  function getMemberId(memberName) {
    // Example: If you have IDs stored in a JSON file
    let teamMembers = JSON.parse(localStorage.getItem('teamMembers'));
    let member = teamMembers.find(m => m.name === memberName);
    if (member) {
      return member.id;
    } else {
      return null;
    }
  }