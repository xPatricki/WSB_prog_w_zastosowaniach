import "./bootstrap"

// Initialize countdown timers on the My Books page
document.addEventListener("DOMContentLoaded", () => {
  const countdownTimers = document.querySelectorAll(".countdown-timer")

  countdownTimers.forEach((timer) => {
    if (timer.dataset.overdue === "true") {
      return
    }

    let days = Number.parseInt(timer.dataset.days)
    let hours = Number.parseInt(timer.dataset.hours)
    let minutes = Number.parseInt(timer.dataset.minutes)
    let seconds = Number.parseInt(timer.dataset.seconds)

    const daysElement = timer.querySelector(".days")
    const hoursElement = timer.querySelector(".hours")
    const minutesElement = timer.querySelector(".minutes")
    const secondsElement = timer.querySelector(".seconds")

    const interval = setInterval(() => {
      seconds--

      if (seconds < 0) {
        seconds = 59
        minutes--

        if (minutes < 0) {
          minutes = 59
          hours--

          if (hours < 0) {
            hours = 23
            days--

            if (days < 0) {
              clearInterval(interval)
              timer.innerHTML = `
                                <div class="text-danger d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                                    Overdue
                                </div>
                            `
              return
            }

            if (daysElement) daysElement.textContent = days
          }

          if (hoursElement) hoursElement.textContent = hours.toString().padStart(2, "0")
        }

        if (minutesElement) minutesElement.textContent = minutes.toString().padStart(2, "0")
      }

      if (secondsElement) secondsElement.textContent = seconds.toString().padStart(2, "0")
    }, 1000)
  })
})

