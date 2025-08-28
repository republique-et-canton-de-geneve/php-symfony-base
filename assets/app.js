/*jshint esversion: 6 */
"use strict";
import Cookies from "js-cookie";
import "./bootstrap.js";

import "bootstrap";
import "./styles/app.scss";

const pageMessageId = "EDG-9999-pagemessageid";

// Ajout du padding au body par rapport à la taille de la navbar fixed
const navbar = document.querySelector(".navbar.fixed-top");
if (navbar) {
  const offset = 20;
  document.body.style.paddingTop = `${offset + navbar.offsetHeight}px`;
}

// Permet d'eviter la double redirection si l'utilisateur double clique sur le lien
const linkDisabledOnClick = Array.from(
  document.getElementsByClassName("link-disabled-onclick"),
);
linkDisabledOnClick.forEach((element) => {
  element.addEventListener("click", () => {
    element.classList.add("disabled");
  });
});

// Permet d'eviter le double submit si l'utilisateur double clique sur le bouton
const formPreventDoubleSubmission = Array.from(
  document.getElementsByClassName("form-prevent-double-submission"),
);
formPreventDoubleSubmission.forEach((element) => {
  element.addEventListener("submit", (e) => {
    if (element.getAttribute("data-submitted")) {
      e.preventDefault();
    } else {
      element.setAttribute("data-submitted", "true");
    }
  });
});

const pageInfo = document.getElementById("page-info");
if (pageInfo) {
  const id = pageInfo.dataset.id;
  const cookieId = Cookies.get(pageMessageId);
  if (id !== cookieId) {
    pageInfo.classList.remove("d-none");
  }
  pageInfo.addEventListener("closed.bs.alert", () => {
    Cookies.set(pageMessageId, id, {
      secure: true,
      sameSite: "strict",
      expires: 1,
    });
  });
} else {
  Cookies.remove(pageMessageId);
}
