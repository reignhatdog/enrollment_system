
window.addEventListener("load", () => {
  setTimeout(() => {
    const loadingScreen = document.getElementById("loading-screen")
    const mainContent = document.getElementById("main-content")

    
    loadingScreen.style.opacity = "0"

    setTimeout(() => {
      loadingScreen.style.display = "none"
      mainContent.classList.remove("hidden")
      document.body.style.overflow = "auto"

    
      setTimeout(() => {
        mainContent.classList.add("show")
      }, 100)
    }, 1000)
  }, 2500)
})

document.getElementById("loginForm").addEventListener("submit", (e) => {
  e.preventDefault()

  const username = document.getElementById("username").value
  const password = document.getElementById("password").value
  const errorMessage = document.getElementById("error-message")

  errorMessage.classList.add("hidden")

  if (username === "reign" && password === "admin") {

    document.getElementById("loginForm").submit()
  } else {
    errorMessage.textContent = "Invalid username or password"
    errorMessage.classList.remove("hidden")
  }
})
