document.addEventListener("DOMContentLoaded", () => {
  let activables = document.querySelectorAll(".activable");

  activables.forEach((activable) => {
    activable.addEventListener("click", (event) => {
      event.currentTarget.classList.toggle("active");
    });
  });
});
