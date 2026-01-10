/** 
 * Booking Countdown Timer Script
 * Auto cancel booking if not paid within 10 minutes
 */

class BookingCountDown{
    constructor(options = {}) {
        // Default settings
        this.timeleft = options.timeleft || 600; // 10 minutes in seconds
        this.countdownElement = document.getElementById(options.elementId || 'countdown');
        this.redirectUrl = options.redirectUrl || '/'; // URL to redirect on expiration
        this.warningThreshold = options.warningThreshold || 60; // 1 minute
        //Validate elements - necessary when JS is loaded but DOM is not fully ready
        if (!this.countdownElement) {
            console.error('Countdown element not found');
            return;
        }
        // Start the countdown
        this.startCountdown();
    }
    //* Start the countdown timer */
    startCountdown() {
        //update immediately
        this.updateDisplay();

        //update every second
        this.timer = setInterval(() => {
            this.timeleft--;
            this.updateDisplay();

            //Check if time has expired
            if (this.timeleft <= 0) {
                clearInterval(this.timer);
                this.handleTimeout();
            }
            //Optional: Add warning style when below threshold
            else if (this.timeleft === this.warningThreshold) {
                this.countdownElement.style.color = 'red';
            }
        }, 1000);//1000 is milliseconds
    }
    /* Update the countdown display */
    updateDisplay() {
        //check if element exists
        if (!this.countdownElement) return; //safety check
        const minutes = Math.floor(this.timeleft / 60);
        const seconds = this.timeleft % 60; //convert time to minutes and seconds

        //Format display
        const formattedTime = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        this.countdownElement.textContent = formattedTime;
}
    /** Warning style - optional*/
    setWarningStyle() {
        if (this.countdownElement) {
            this.countdownElement.style.color = 'red';
            this.countdownElement.style.fontWeight = 'bold';
        }
    }
    /* Handle expiration - redirect or notify */
    handleTimeout() {
        //Stop timer
        if (this.timer) {
            clearInterval(this.timer);
        }
        //Notify user and redirect
        if (this.countdownElement) {
            this.countdownElement.textContent = '00:00';
            this.countdownElement.style.color = 'gray';
            this.countdownElement.style.fontWeight = 'normal';
        }
        //Show alert and redirect
        alert('Booking time has expired. You will be redirected to the homepage.');
        //Redirect to homepage
        setTimeout(() => {
            window.location.href = this.redirectUrl;
        }, 2000); //2 second delay before redirect
    }
}
//Auto-initialize on DOM load
document.addEventListener('DOMContentLoaded', () => {
    const countdown = document.getElementById('countdown');
    if (countdown){
        //Start countdown
        new BookingCountDown({
            timeleft: 600, //10 minutes
            elementId: 'countdown',
            redirectUrl: '/', //redirect to homepage
            warningThreshold: 60 //1 minute warning
        });
    }
});