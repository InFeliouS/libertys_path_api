// public/js/dashboard_cards.js

document.addEventListener("DOMContentLoaded", () => {
  const container = document.getElementById("sectionCards");
  if (!container) return;

  fetch("/api/v1/sections.php", { headers: { "Accept": "application/json" } })
    .then(res => {
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      return res.json();
    })
    .then(json => {
      if (!json.success) throw new Error(json.message || "API error");
      container.innerHTML = ""; // clear placeholder

      json.sections.forEach(sec => {
        const card = document.createElement("div");
        card.className = "section-card";

        const title = document.createElement("h3");
        title.textContent = sec.section_name;
        card.appendChild(title);

        const years = document.createElement("p");
        years.innerHTML = `${sec.start_school_year} &ndash; ${sec.end_school_year}`;
        card.appendChild(years);

        const btn = document.createElement("button");
        btn.textContent = "View Students";
        btn.onclick = () => {
          location.href = `/sections/view?section_id=${sec.id}`;
        };
        card.appendChild(btn);

        container.appendChild(card);
      });
    })
    .catch(err => {
      console.error("Could not load sections:", err);
      container.innerHTML = "<p class='error'>Failed to load sections.</p>";
    });
});
