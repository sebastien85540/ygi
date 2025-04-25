# 🌿 YGI – Site WordPress éco-conçu

Site web officiel de YGI, un projet engagé pour le bien-être et l’accompagnement à la santé à travers des ateliers, parcours de soins et événements accessibles.  
Ce dépôt contient la base technique du site WordPress, son infrastructure personnalisée, les plugins utilisés, ainsi que les instructions de développement et maintenance.

---

## 🚀 Objectifs du site

- Présenter les **événements** (ateliers, cours collectifs…)
- Mettre en avant un **parcours de soins**
- Permettre la **prise de contact** et **l’inscription** à des activités
- Valoriser les témoignages
- Respecter les bonnes pratiques d’**éco-conception web**
- Préparer l’intégration de la **réservation** et des **avis utilisateurs** (v2)

---

## 🧱 Structure du site

- **Technologie** : WordPress 6.8
- **Thème** : Personnalisé (basé sur l’éditeur de blocs natif)
- **Langue** : Français
- **Rôle actif** : Administrateur uniquement (visiteurs en lecture seule)
- **Accès admin** : via `/wp-admin`

---

## 🔌 Plugins utilisés

| Plugin | Usage |
|--------|-------|
| [Advanced Custom Fields (ACF)](https://www.advancedcustomfields.com/) | Champs personnalisés pour les événements |
| [Akismet](https://akismet.com/) | Protection anti-spam |
| [Booking Activities](https://booking-activities.fr/) | (Prévu) Réservation d’activités |
| [Contact Form 7](https://contactform7.com/) | Formulaire de contact |
| [Simple Custom CSS and JS](https://wordpress.org/plugins/custom-css-js/) | CSS personnalisé (par ex. couleur des liens actifs) |
| [Custom Post Type UI](https://wordpress.org/plugins/custom-post-type-ui/) | Gestion des CPT comme "Événements" ou "Témoignages" |
| [Easy Accordion Free](https://wordpress.org/plugins/easy-accordion-free/) | Accordeons d'information |
| [Strong Testimonials](https://wordpress.org/plugins/strong-testimonials/) | Affichage manuel des témoignages |

---

## 📝 Contenu personnalisé (CPT)

- `événements` : chaque événement comprend un titre, une description, une date, un bouton d’inscription externe.
- `témoignages` : ajoutés manuellement via le back-office par l’administrateur.

---

## 🎨 Personnalisation CSS

Ajout via l’extension **Custom CSS and JS** :

```css
.wp-block-navigation-item.current-menu-item > .wp-block-navigation-item__content {
    color: #FFFFFF !important;
    background-color: #f5ea3e;
}

