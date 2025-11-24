# Single Grant Page - Comprehensive Refactoring Plan

## 📋 Overview
This document outlines the complete refactoring strategy for `single-grant.php` to improve maintainability, performance, and SEO.

## 🎯 Primary Goals
1. **Code Organization**: Extract logic from template to inc/ files
2. **Performance**: Separate CSS/JS, enable browser caching
3. **Maintainability**: Use unified formatting functions
4. **SEO**: Optimize HTML structure, add missing features
5. **UX**: Improve mobile experience, add social features

## 🏗️ Architecture Changes

### Phase 1: Logic Extraction ✅ (COMPLETED)
- ✅ Created `inc/grant-data-helper.php` - Data retrieval abstraction layer
- ✅ Moved recommendation logic to `inc/ai-functions.php`
- 📝 Next: Extract formatting functions to `GrantCardRenderer`

### Phase 2: Asset Separation (IN PROGRESS)
#### CSS Files to Create:
- `assets/css/single-grant.css` - Main styles (~700 lines)
  - Responsive layout system
  - Component styles (tables, cards, buttons)
  - Mobile-specific styles
  
#### JS Files to Create:
- `assets/js/single-grant.js` - Main functionality (~300 lines)
  - Carousel navigation
  - AI chat interface
  - FAQ accordion
  - TOC (Table of Contents) active highlighting

### Phase 3: HTML Structure Optimization
#### Current Issues:
1. **Duplicate Content**: PC table + Mobile cards (双方に同じデータ)
2. **Inline CSS**: 700+ lines mixed with PHP
3. **Missing Features**: Related columns, view counts, favorites

#### Proposed Structure:
```html
<main class="gus-single">
    <article class="gus-main">
        <!-- Hero Section with Key Info -->
        <header class="gus-header">
            <div class="gus-status-badges"></div>
            <h1></h1>
            <div class="gus-hero-grid">
                <!-- Single source, CSS Grid for PC/SP -->
            </div>
        </header>
        
        <!-- AI Summary -->
        <section class="gus-ai-summary"></section>
        
        <!-- Main Content -->
        <div class="gus-content"></div>
        
        <!-- Related Columns (NEW) -->
        <section class="gus-related-columns"></section>
        
        <!-- Recommended Grants - Grid (NOT Carousel) -->
        <section class="gus-recommendations-grid"></section>
    </article>
    
    <aside class="gus-sidebar gus-sidebar--sticky">
        <!-- CV Button -->
        <!-- TOC -->
        <!-- AI Chat (Floating on mobile) -->
        <!-- Ads -->
    </aside>
</main>
```

## 🔧 Function Refactoring Map

### Move from `single-grant.php` to:

#### `inc/grant-data-helper.php` ✅
- `GI_Grant_Data_Helper::get_all_data()` - Unified data retrieval
- `GI_Grant_Data_Helper::format_prefectures()` - Prefecture formatting
- `GI_Grant_Data_Helper::format_municipalities()` - Municipality formatting
- `GI_Grant_Data_Helper::get_deadline_info()` - Deadline calculation
- `GI_Grant_Data_Helper::generate_meta_description()` - SEO meta
- `GI_Grant_Data_Helper::calculate_reading_time()` - Reading time

#### `inc/ai-functions.php` ✅
- `gi_get_scored_related_grants()` - Recommendation engine

#### `inc/card-display.php` (UPDATE NEEDED)
- `GrantCardRenderer::format_amount()` - Make public static
- `GrantCardRenderer::format_deadline()` - Make public static
- Add `GrantCardRenderer::format_currency()` - 金額の統一表示

## 📊 New Features to Add

### 1. Related Columns Integration
```php
// Use existing column-system.php function
$related_columns = gi_get_columns_by_grant($post_id, 6);
```

### 2. View Counter & Favorites
```php
// Display from access-tracking.php
$views = gi_get_post_views($post_id);
$is_favorite = gi_is_user_favorite($post_id);
```

### 3. Social Sharing
- Twitter
- Facebook
- LINE
- 印刷 (Print)

### 4. Floating AI Chat (Mobile)
- Move from sidebar to floating button
- Click to expand chat interface

## 🎨 CSS Structure (Proposed)

### File: `assets/css/single-grant.css`
```css
/* 1. CSS Variables */
:root { /* Design tokens */ }

/* 2. Layout System */
.gus-single { /* Container */ }
.gus-layout { /* Grid layout */ }

/* 3. Components */
.gus-header { /* ... */ }
.gus-hero-grid { /* Responsive grid */ }
.gus-table-responsive { /* Single table, responsive */ }
.gus-ai-chat { /* ... */ }
.gus-recommendations-grid { /* 2-3 column grid */ }

/* 4. Mobile Overrides */
@media (max-width: 768px) { /* ... */ }
```

## 🚀 Implementation Priority

### 🔴 High Priority (This Session)
1. ✅ Create `inc/grant-data-helper.php`
2. ✅ Move recommendation logic to `inc/ai-functions.php`
3. 📝 Update `GrantCardRenderer` with static formatting methods
4. 📝 Create basic CSS/JS file structure

### 🟡 Medium Priority (Next Session)
5. Extract CSS to external file
6. Extract JS to external file
7. Update `single-grant.php` to use new structure
8. Add related columns display
9. Add view counter and favorites
10. Implement responsive table (single HTML source)

### 🟢 Low Priority (Future Enhancement)
11. Social sharing buttons
12. Print functionality
13. Floating AI chat for mobile
14. Grid display for recommendations

## 📝 Code Quality Improvements

### Before (Current Issues):
- ❌ 2000+ lines in single template file
- ❌ 700 lines of inline CSS
- ❌ 300 lines of inline JS
- ❌ Duplicate HTML (PC/SP)
- ❌ Inconsistent formatting (同じ金額でも表示が違う)
- ❌ No caching for assets

### After (Target):
- ✅ ~500 lines in template (mostly HTML)
- ✅ Separate, cacheable CSS/JS files
- ✅ Single HTML source with responsive CSS
- ✅ Unified formatting via `GrantCardRenderer`
- ✅ Better browser caching and performance

## 🔗 Integration with Existing Systems

### Already Available (Use These):
- `inc/affiliate-ad-manager.php` - `ji_display_ad()`
- `inc/access-tracking.php` - `gi_track_view()`, `gi_get_post_views()`
- `inc/card-display.php` - `GrantCardRenderer::render()`
- `inc/column-system.php` - `gi_get_columns_by_grant()`
- `inc/acf-fields.php` - ACF field wrappers
- `inc/grant-amount-fixer.php` - Ensures data consistency

## 🎯 Success Metrics

### Technical:
- ⏱️ Page load time: Target < 2s
- 📦 HTML size reduction: ~40% (remove duplicate content)
- 🔄 Browser cache hit rate: ~80% for returning visitors

### SEO:
- 🔍 Better crawlability (cleaner HTML)
- 📊 Improved Core Web Vitals (LCP, FID, CLS)
- 🔗 More internal links (related columns)

### UX:
- 📱 Consistent PC/SP experience
- 🎨 Easier design updates (CSS only)
- ⚡ Faster perceived performance

## 🚦 Current Status

### Completed:
- ✅ Data helper class created
- ✅ Recommendation logic extracted
- ✅ Refactoring plan documented

### In Progress:
- 🔄 Formatting functions unification
- 🔄 CSS/JS extraction planning

### Pending:
- ⏳ CSS file creation
- ⏳ JS file creation
- ⏳ Template simplification
- ⏳ New features (columns, social, etc.)

## 📌 Notes for Next Session

1. Start with `GrantCardRenderer` static methods
2. Create base CSS file with critical styles
3. Create base JS file with essential functionality
4. Update `single-grant.php` incrementally
5. Test on real data after each major change
6. Commit frequently with descriptive messages

## 🔍 Testing Checklist

- [ ] PC display works correctly
- [ ] Mobile display works correctly
- [ ] Tablet display works correctly
- [ ] All data fields display correctly
- [ ] Related grants show properly
- [ ] Related columns show (if available)
- [ ] AI chat functions
- [ ] TOC navigation works
- [ ] Breadcrumbs display
- [ ] Meta tags are correct
- [ ] Structured data validates
- [ ] Page speed is acceptable

---

**Last Updated**: 2025-11-24
**Status**: Phase 1 Complete, Phase 2 In Progress
**Next Action**: Update GrantCardRenderer with static methods
