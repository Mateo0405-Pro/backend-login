window.onload = function () {
  window.ui = SwaggerUIBundle({
    url: "/public/swagger.json", // o solo "/swagger.json" si está en public directamente
    dom_id: "#swagger-ui",
    deepLinking: true,
    presets: [
      SwaggerUIBundle.presets.apis,
      SwaggerUIStandalonePreset
    ],
    layout: "StandaloneLayout"
  });
};