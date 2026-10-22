# UNCART - Project & Design Guidelines

This workspace is powered by three top-tier frontend design engineering skills:
1. **Emil Kowalski's Design Engineering & Motion Philosophy** (`emil-design-eng`, `animate`, etc.)
2. **Impeccable Design System** (`impeccable`)
3. **Taste Skill & Anti-Slop Art Direction** (`taste-skill` / `design-taste-frontend`)

---

## 1. Emil Kowalski's Design Engineering Principles

- **Taste is trained & deliberate**: Avoid generic defaults. Build interfaces where micro-interactions, responsive states, and transitions feel natural and effortless.
- **Micro-interactions & Physics**:
  - Keep interactive animations fast: under `250ms - 300ms` for standard interactions.
  - Never use `ease-in` for entering elements. Use crisp decelerating curves or spring physics (e.g. `cubic-bezier(0.16, 1, 0.3, 1)`).
  - Popovers, tooltips, and modals should scale subtly from their trigger or anchor rather than popping from `scale(0)` or center.
  - Elevation: Favor subtle layered borders and soft, diffused shadows over heavy drop-shadows or solid dark borders.
  - Layout stability: Prevent layout shifts (CLS) by reserving space for dynamically loaded content or animated state toggles.
- **Respect Motion Preferences**:
  - Always support `@media (prefers-reduced-motion: reduce)` with clean zero-duration or instant fallbacks.

---

## 2. Impeccable Design Guidelines

- **No AI Slop**: Outlaw generic templates, predictable purple-blue hero gradients, cookie-cutter 3-card grids, and default Inter font styling without hierarchy.
- **Mode-Driven Surface Design**:
  - **Persuade (Landing, Marketing, Hero)**: High point-of-view, confident scale contrast, memorable typography, distinct color anchors, and rich textural depth.
  - **Operate (Dashboards, E-commerce, Workflows)**: High clarity, compact information density, keyboard accessibility, speed, and zero ambiguity.
  - **Read (Articles, Documentation, Details)**: Comfortable line lengths (`45-75ch`), harmonious line heights (`1.5-1.7`), clean optical margins.
- **Craft Floor**:
  - Hierarchy first: Size, weight, color contrast, and spacing must clearly convey primary vs secondary vs tertiary information.
  - Contrast ratios must meet WCAG AA standards.
  - Crisp form controls with clear focus rings (`:focus-visible`), active states, error states, and loading indicators.

---

## 3. Taste Skill Art Direction

- **Curated Visual Identity**:
  - Pick intentional, distinct typography pairings with personality (e.g., editorial serif paired with precision grotesque sans, or characterful geometric display).
  - Use deliberate palettes: 1–2 strong anchor colors, controlled neutral ramps, and purposeful semantic accents.
  - Asymmetry and breathing room: Balance solid structured grids with asymmetric focal points and generous white space.
  - Contextual polish: Check and verify rendered output across screen sizes (mobile, tablet, desktop).

---

## Active Skills in Workspace

The following skills are installed in `.agents/skills/` and `.agent/skills/`:
- `emil-design-eng`: Core UI polish, layout details, and component craft.
- `animate`: Motion, easing curves, choreography, and spring animations.
- `animation-performance`: GPU acceleration, transform/opacity isolation, repaint avoidance.
- `animation-vocabulary`: Animation patterns, physics properties, timing scales.
- `apple-design`: Human Interface craft, fluid gestures, refined feedback.
- `ask-sonner`: Toast notifications and contextual micro-feedback.
- `review-animations` / `improve-animations`: Auditing and elevating motion.
- `taste-skill` / `design-taste-frontend`: Intentional art direction and anti-slop rules.
- `impeccable`: Design audit, design tokens, craft floor, typography and palette shaping.
