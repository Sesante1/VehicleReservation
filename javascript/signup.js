// const form = document.querySelector(".signup form"),
//   continueBtn = form.querySelector(".button input"),
//   errorText = form.querySelector(".error-text");

// form.onsubmit = (e) => {
//   e.preventDefault();
// }

// continueBtn.onclick = () => {
//   let xhr = new XMLHttpRequest();
//   xhr.open("POST", "php/signup.php", true);
//   xhr.onload = () => {
//     if (xhr.readyState === XMLHttpRequest.DONE) {
//       if (xhr.status === 200) {
//         let data = xhr.response;
//         if (data === "success") {
//           location.href = "users.php";
//         } else {
//           errorText.style.display = "block";
//           errorText.textContent = data;
//         }
//       }
//     }
//   }
//   let formData = new FormData(form);
//   xhr.send(formData);
// }

// const form = document.querySelector(".signup form"),
//   continueBtn = form.querySelector(".button input"),
//   errorText = form.querySelector(".error-text");

// form.onsubmit = (e) => {
//   e.preventDefault();
// }

// continueBtn.onclick = () => {
//   let xhr = new XMLHttpRequest();
//   xhr.open("POST", "php/signup.php", true);
//   xhr.onload = () => {
//     if (xhr.readyState === XMLHttpRequest.DONE) {
//       if (xhr.status === 200) {
//         let data = xhr.response;
//         if (data === "success") {
//           // Instead of directly changing location, use history API and handleLocation
//           window.history.pushState({}, "", "/users");
//           handleLocation();
//         } else {
//           errorText.style.display = "block";
//           errorText.textContent = data;
//         }
//       }
//     }
//   }
//   let formData = new FormData(form);
//   xhr.send(formData);
// }

(() => {
  const form = document.querySelector(".signup form");
  if (!form) return;

  const continueBtn = form.querySelector(".button input");
  const errorText = form.querySelector(".error-text");

  form.onsubmit = (e) => {
    e.preventDefault();
  };

  continueBtn.onclick = () => {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "php/signup.php", true);
    xhr.onload = () => {
      if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
        const data = xhr.response;
        if (data === "success") {
          location.href = "/";
        } else {
          errorText.style.display = "block";
          errorText.textContent = data;
        }
      }
    };
    const formData = new FormData(form);
    xhr.send(formData);
  };
})();
