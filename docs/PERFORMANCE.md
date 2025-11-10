# Architect's Performance Log (v1.2.4)

This document serves as the official performance and accessibility baseline for the `upulsanjeewagamage.com` custom theme.

As an "AI-Driven Architect," my standard is to be high-performance by default. This site is built on a minimal, "bloat-free" custom theme.

## v1.2.4 Baseline Scores (Mobile)

These scores were captured on **November 10, 2025,** via Google PageSpeed Insights.

| Metric | Score (Mobile) |
| :--- | :--- |
| **Performance** | **[99]** / 100 |
| **Accessibility** | **[94]** / 100 |
| **Best Practices** | **[81]** / 100 |
| **SEO** | **[100]** / 100 |

## Performance Budget (v1.2.x Branch)

My goal for this theme is to maintain:
* **LCP (Largest Contentful Paint):** Under 2.5s
* **CLS (Cumulative Layout Shift):** Under 0.1
* **PageSpeed Performance Score:** 95+ at all times.

## Known v1.1 Refactor Notes

* **Conditional Loading:** The AI audit correctly noted that plugin assets (`fluentform.css`) are loading globally. A future v1.3 task will be to write a PHP function to *conditionally* load these assets *only* on the pages where the form is present (Homepage, Architect's Journal).
