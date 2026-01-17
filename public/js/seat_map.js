/**
 * Seat Map JavaScript - UI/UX Only
 * Handles seat selection interactions and visual feedback
 * Price calculation is for display purposes only (not trusted by server)
 */

document.addEventListener('DOMContentLoaded', function() {
    const seatButtons = document.querySelectorAll('.seat-btn.available');
    const selectedSeatIds = document.getElementById('selectedSeatIds');
    const seatList = document.getElementById('seatList');
    const bookBtn = document.getElementById('bookBtn');
    
    let selectedSeats = [];
    
    // Define estimated prices for UI display (not used for actual billing)
    const ESTIMATED_PRICES = {
        1: 50000,   // Standard - estimated
        2: 60000,   // VIP - estimated  
        3: 80000    // Couple - estimated
    };
    
    seatButtons.forEach(button => {
        button.addEventListener('click', function() {
            const seatId = this.getAttribute('data-seat-id');
            const seatCode = this.getAttribute('data-seat-code');
            const seatType = parseInt(this.getAttribute('data-seat-type'));
            
            const rowLetter = seatCode.match(/^[A-Z]+/)[0];
            
            if (rowLetter === 'H') {
                handleCoupleSeatLogic(this, seatId, seatCode, seatType);
            } else {
                handleRegularSeatLogic(this, seatId, seatCode, seatType);
            }
            
            updateSelectedSeatsDisplay();
        });
    });
    
    function handleCoupleSeatLogic(button, seatId, seatCode, seatType) {
        // Handle couple seat logic (H1-2, H3-4, H5-6, ...)
        const seatNumber = parseInt(seatCode.match(/\d+$/)[0]);
        const rowLetter = seatCode.match(/^[A-Z]+/)[0];
        
        let pairSeatNumber;
        if (seatNumber % 2 === 1) {
            pairSeatNumber = seatNumber + 1;
        } else {
            pairSeatNumber = seatNumber - 1;
        }
        const pairSeatCode = rowLetter + pairSeatNumber;
        const pairSeatButton = document.querySelector(`[data-seat-code="${pairSeatCode}"]`);
        
        if (selectedSeats.find(seat => seat.id === seatId)) {
            // Deselect both seats in the pair
            deselectSeat(button, seatId);
            
            if (pairSeatButton) {
                const pairSeatId = pairSeatButton.getAttribute('data-seat-id');
                deselectSeat(pairSeatButton, pairSeatId);
            }
        } else {
            // Select both seats in the pair
            if (pairSeatButton && pairSeatButton.classList.contains('available')) {
                const pairSeatId = pairSeatButton.getAttribute('data-seat-id');
                const pairSeatType = parseInt(pairSeatButton.getAttribute('data-seat-type'));
                
                selectSeat(button, seatId, seatCode, seatType);
                selectSeat(pairSeatButton, pairSeatId, pairSeatCode, pairSeatType);
            } else {
                alert('Couple seat selection failed.');
                return;
            }
        }
    }
    
    function handleRegularSeatLogic(button, seatId, seatCode, seatType) {
        if (selectedSeats.find(seat => seat.id === seatId)) {
            deselectSeat(button, seatId);
        } else {
            selectSeat(button, seatId, seatCode, seatType);
        }
    }
    
    function selectSeat(button, seatId, seatCode, seatType) {
        // Store original color for deselection
        if (!button.getAttribute('data-original-color')) {
            button.setAttribute('data-original-color', button.style.backgroundColor);
        }
        
        // Add to selected seats array
        selectedSeats.push({
            id: seatId,
            code: seatCode,
            type: seatType,
            estimatedPrice: ESTIMATED_PRICES[seatType] || ESTIMATED_PRICES[1]
        });
        
        // Update visual appearance
        button.style.backgroundColor = '#3498db';
        button.style.border = '2px solid #2980b9';
    }
    
    function deselectSeat(button, seatId) {
        // Remove from selected seats array
        selectedSeats = selectedSeats.filter(seat => seat.id !== seatId);
        
        // Restore original appearance
        const originalColor = button.getAttribute('data-original-color');
        if (originalColor) {
            button.style.backgroundColor = originalColor;
        } else {
            // Fallback to default colors based on seat type
            const seatType = parseInt(button.getAttribute('data-seat-type'));
            button.style.backgroundColor = getSeatTypeColor(seatType);
        }
        button.style.border = '2px solid transparent';
    }
    
    function getSeatTypeColor(seatType) {
        switch(seatType) {
            case 2: return '#f1c40f'; // VIP
            case 3: return '#e84393'; // Couple
            default: return '#2ecc71'; // Standard
        }
    }
    
    function updateSelectedSeatsDisplay() {
        if (selectedSeats.length === 0) {
            seatList.textContent = 'None';
            selectedSeatIds.value = '';
            bookBtn.disabled = true;
        } else {
            const seatCodes = selectedSeats.map(seat => seat.code).join(', ');
            seatList.textContent = seatCodes;

            // Send only seat IDs to server
            const seatIds = selectedSeats.map(seat => seat.id);
            selectedSeatIds.value = JSON.stringify(seatIds);

            bookBtn.disabled = false;
        }
    }
});