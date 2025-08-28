import "./app.js";
import { ClassicEditor } from "@ckeditor/ckeditor5-editor-classic";
import { Essentials } from "@ckeditor/ckeditor5-essentials";
import { Bold, Italic, Underline } from "@ckeditor/ckeditor5-basic-styles";
import { Alignment } from "@ckeditor/ckeditor5-alignment";
import { Paragraph } from "@ckeditor/ckeditor5-paragraph";
import "./ckeditor.css";

ClassicEditor.builtinPlugins = [
  Essentials,
  Bold,
  Italic,
  Underline,
  Alignment,
  Paragraph,
];
ClassicEditor.defaultConfig = {
  language: "fr",
};

ClassicEditor
  // Note that you do not have to specify the plugin and toolbar configuration — using defaults from the build.
  .create(document.querySelector(".ckeditor"), {
    licenseKey: "",
    toolbar: [
      "undo",
      "redo",
      "|",
      "bold",
      "italic",
      "underline",
      "alignment:left",
      "alignment:center",
      "alignment:right",
    ],
  })
  .catch((x) => {
    console.log("erreur :" + x);
  });
