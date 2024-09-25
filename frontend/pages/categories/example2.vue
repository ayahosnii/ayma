<template>
  <div class="category-table">
    <ul>
      <!-- Loop through categories -->
      <li v-for="(category, index) in categories" :key="index" class="category-item">
        <div class="category-row">
          <!-- Main category with collapse/expand button -->
          <button @click="toggleCategory(index)" class="collapse-btn">
            <i :class="category.expanded ? 'icon-expanded' : 'icon-collapsed'"></i>
          </button>
          <span class="category-name">{{ category.name }} ({{ category.count }})</span>
          <span class="category-products">{{ category.products }}</span>
          <span class="category-status" :class="category.active ? 'status-active' : 'status-inactive'">
            {{ category.active ? 'Active' : 'Inactive' }}
          </span>
          <button class="edit-btn">Edit</button>
        </div>

        <!-- Subcategories (collapsible) -->
        <ul v-if="category.expanded" class="subcategory-list">
          <li v-for="(subcategory, subIndex) in category.subcategories" :key="subIndex" class="subcategory-item">
            <div class="subcategory-row">
              <button v-if="subcategory.subcategories" @click="toggleSubcategory(index, subIndex)" class="collapse-btn">
                <i :class="subcategory.expanded ? 'icon-expanded' : 'icon-collapsed'"></i>
              </button>
              <span class="subcategory-name">{{ subcategory.name }} ({{ subcategory.count }})</span>
              <span class="subcategory-products">{{ subcategory.products }}</span>
              <span class="subcategory-status" :class="subcategory.active ? 'status-active' : 'status-inactive'">
                {{ subcategory.active ? 'Active' : 'Inactive' }}
              </span>
              <button class="edit-btn">Edit</button>
            </div>

            <!-- Nested Subcategories (if any) -->
            <ul v-if="subcategory.expanded && subcategory.subcategories" class="nested-subcategory-list">
              <li v-for="(nestedSubcategory, nestedIndex) in subcategory.subcategories" :key="nestedIndex">
                <div class="nested-subcategory-row">
                  <span class="subcategory-name">{{ nestedSubcategory.name }} ({{ nestedSubcategory.count }})</span>
                  <span class="subcategory-products">{{ nestedSubcategory.products }}</span>
                  <span class="subcategory-status" :class="nestedSubcategory.active ? 'status-active' : 'status-inactive'">
                    {{ nestedSubcategory.active ? 'Active' : 'Inactive' }}
                  </span>
                  <button class="edit-btn">Edit</button>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      categories: [
        {
          name: 'Clothes',
          count: 14,
          products: '3 products',
          active: true,
          expanded: false,
          subcategories: [
            {
              name: 'Clothes for women',
              count: 7,
              products: '5 products',
              active: false,
              expanded: false,
              subcategories: [
                { name: 'Dresses', count: 3, products: '2 products', active: true },
                { name: 'Tops', count: 2, products: '1 product', active: false },
              ],
            },
            {
              name: 'Clothes for Kids',
              count: 2,
              products: '-',
              active: true,
              expanded: false,
              subcategories: [
                { name: 'Fall-Winter 2017', count: 0, products: '-', active: true },
                { name: 'Spring-Summer 2018', count: 0, products: '-', active: true },
              ],
            },
          ],
        },
        {
          name: 'Shoes',
          count: 15,
          products: '9 products',
          active: true,
          expanded: false,
          subcategories: [
            {
              name: 'Men’s Shoes',
              count: 8,
              products: '4 products',
              active: true,
              expanded: false,
              subcategories: [
                { name: 'Casual Shoes', count: 4, products: '2 products', active: true },
                { name: 'Formal Shoes', count: 4, products: '2 products', active: false },
              ],
            },
            {
              name: 'Women’s Shoes',
              count: 7,
              products: '5 products',
              active: false,
              expanded: false,
              subcategories: [
                { name: 'High Heels', count: 3, products: '2 products', active: true },
                { name: 'Sneakers', count: 4, products: '3 products', active: true },
              ],
            },
          ],
        },
        {
          name: 'Electronics',
          count: 20,
          products: '15 products',
          active: true,
          expanded: false,
          subcategories: [
            {
              name: 'Laptops',
              count: 8,
              products: '5 products',
              active: true,
              expanded: false,
              subcategories: [
                { name: 'Gaming Laptops', count: 2, products: '1 product', active: true },
                { name: 'Business Laptops', count: 3, products: '2 products', active: true },
                { name: 'Ultrabooks', count: 3, products: '2 products', active: false },
              ],
            },
            {
              name: 'Smartphones',
              count: 12,
              products: '7 products',
              active: true,
              expanded: false,
              subcategories: [
                { name: 'Android', count: 7, products: '4 products', active: true },
                { name: 'iPhones', count: 5, products: '3 products', active: false },
              ],
            },
          ],
        },
        {
          name: 'Health and Beauty',
          count: 10,
          products: '5 products',
          active: true,
          expanded: false,
          subcategories: [
            {
              name: 'Skincare',
              count: 5,
              products: '3 products',
              active: true,
              expanded: false,
              subcategories: [
                { name: 'Face Masks', count: 2, products: '1 product', active: true },
                { name: 'Moisturizers', count: 3, products: '2 products', active: true },
              ],
            },
            { name: 'Makeup', count: 5, products: '2 products', active: false, expanded: false },
          ],
        },
        {
          name: 'Furniture',
          count: 9,
          products: '6 products',
          active: false,
          expanded: false,
          subcategories: [
            {
              name: 'Living Room',
              count: 4,
              products: '2 products',
              active: true,
              expanded: false,
              subcategories: [
                { name: 'Sofas', count: 2, products: '1 product', active: true },
                { name: 'Coffee Tables', count: 2, products: '1 product', active: true },
              ],
            },
            {
              name: 'Bedroom',
              count: 5,
              products: '4 products',
              active: false,
              expanded: false,
              subcategories: [
                { name: 'Beds', count: 3, products: '2 products', active: false },
                { name: 'Wardrobes', count: 2, products: '1 product', active: true },
              ],
            },
          ],
        },
      ],
    };
  },
  methods: {
    toggleCategory(index) {
      this.categories[index].expanded = !this.categories[index].expanded;
    },
    toggleSubcategory(categoryIndex, subIndex) {
      this.categories[categoryIndex].subcategories[subIndex].expanded = !this.categories[categoryIndex].subcategories[subIndex].expanded;
    },
  },
};
</script>

<style scoped>
/* General Styling */
ul {
  list-style-type: none;
  padding-left: 0;
}

li {
  margin: 5px 0;
}

/* Category Row */
.category-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 20px;
  background-color: #fff;
  border-bottom: 1px solid #e0e0e0;
  cursor: pointer;
}

.category-row:hover {
  background-color: #f4f4f4;
}

/* Collapsible Button */
.collapse-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  margin-right: 10px;
}

.icon-expanded::before {
  content: '▼';
}

.icon-collapsed::before {
  content: '►';
}

/* Category and Subcategory Text */
.category-name,
.subcategory-name {
  font-weight: 600;
  color: #333;
}

.category-products,
.subcategory-products {
  color: #777;
  margin-left: auto;
  margin-right: 10px;
}

.category-status,
.subcategory-status {
  margin-right: 20px;
}

.status-active {
  color: #28a745;
}

.status-inactive {
  color: #dc3545;
}

/* Edit Button */
.edit-btn {
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 5px 10px;
  cursor: pointer;
  font-size: 12px;
}

.edit-btn:hover {
  background-color: #0056b3;
}

/* Subcategory List */
.subcategory-list,
.nested-subcategory-list {
  padding-left: 30px;
  background-color: #fafafa;
  border-left: 2px solid #e0e0e0;
}

.subcategory-row,
.nested-subcategory-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 10px 20px;
}

.subcategory-row:hover,
.nested-subcategory-row:hover {
  background-color: #f4f4f4;
}
</style>
