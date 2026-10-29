<div align="center">

# Programación Web — UTP

**Universidad Tecnológica de Panamá** · Facultad de Ingeniería en Sistemas Computacionales

Coursework and projects for *Programación Web* (Prof. Giovani Sánchez)

`HTML5` · `CSS3` · `PHP` · `MySQL`

</div>

---

## Content

| Folder | What it is | Stack |
| :--- | :--- | :--- |
| [`Proyecto1_Fase2_Henry_Joseph/`](Proyecto1_Fase2_Henry_Joseph/) | Student registration and query portal with login and full CRUD backed by MySQL | PHP · PDO · MySQL |
| [`tarea3_mi_hub_estudiantil/`](tarea3_mi_hub_estudiantil/) | Static student hub: profile page, recommended resources, subject status and contact form | HTML5 · CSS3 |

---

## Proyecto #1 — Fase 2

Extends the Phase 1 portal (records kept in PHP arrays) into a MySQL-backed
application with authentication and complete CRUD.

- `login.php` / `logout.php` — sessions, `password_hash()` / `password_verify()`
- `registro_estudiantes.php` → `procesar_estudiantes.php` — create with validation
- `listar_estudiantes.php` — listing with search by name or cédula
- `editar_estudiante.php` — update with automatic average recalculation
- `eliminar_estudiante.php` — delete with confirmation, POST only
- `database/registro_estudiantes.sql` — schema, constraints, test users, sample data

**Run it**

```bash
sudo cp -r Proyecto1_Fase2_Henry_Joseph /opt/lampp/htdocs/
sudo /opt/lampp/lampp start
sudo /opt/lampp/bin/mysql -u root < /opt/lampp/htdocs/Proyecto1_Fase2_Henry_Joseph/database/registro_estudiantes.sql
```

Open <http://localhost/Proyecto1_Fase2_Henry_Joseph/login.php>
with `admin / Admin2026` or `profesor / Profesor2026`.

Full instructions: [`README.md`](Proyecto1_Fase2_Henry_Joseph/README.md) ·
Technical walkthrough: [`DOCUMENTATION.md`](Proyecto1_Fase2_Henry_Joseph/DOCUMENTATION.md)

---

## Tarea 3 — Mi Hub Estudiantil

Two static pages sharing one stylesheet:

- `index.html` — student profile with photo, personal data and interests
- `recursos.html` — recommended resources, subject status and a contact section

Open `tarea3_mi_hub_estudiantil/index.html` directly in a browser.

---

## Repository layout

```text
.
├── Proyecto1_Fase2_Henry_Joseph/   # PHP + MySQL project (Phase 2)
│   ├── database/
│   ├── CSS/
│   ├── img/
│   └── ...
└── tarea3_mi_hub_estudiantil/      # static HTML/CSS assignment
    ├── css/
    ├── img/
    └── *.html
```

---

## Authors

**Henry Ramirez** · **Joseph Gantes** — Group 1S3221
