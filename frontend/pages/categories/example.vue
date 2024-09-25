<template>
  <div class="category-table">
    <ul>
      <!-- Loop through categories -->
      <li v-for="(category, index) in categories" :key="index">
        <!-- Category row -->
        <div class="category-row" @click="toggleCategory(index)">
          <span class="category-name">{{ category.name }} ({{ category.count }})</span>
          <span class="category-products">{{ category.products }}</span>
          <span class="category-status" :class="category.active ? 'active' : 'inactive'">
            {{ category.active ? 'Active' : 'Inactive' }}
          </span>
          <button class="edit-btn">Edit</button>
        </div>

        <!-- Subcategories (collapsible) -->
        <ul v-if="category.expanded" class="subcategory-list">
          <li v-for="(subcategory, subIndex) in category.subcategories" :key="subIndex">
            <div class="subcategory-row" @click.stop="toggleSubcategory(index, subIndex)">
              <span class="subcategory-name">{{ subcategory.name }} ({{ subcategory.count }})</span>
              <span class="subcategory-products">{{ subcategory.products }}</span>
              <span class="subcategory-status" :class="subcategory.active ? 'active' : 'inactive'">
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
                  <span class="subcategory-status" :class="nestedSubcategory.active ? 'active' : 'inactive'">
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
            { name: 'Clothes for women', count: 7, products: '5 products', active: false, expanded: false },
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
          products: '10 products',
          active: true,
          expanded: false,
          subcategories: [
            { name: 'Men’s Shoes', count: 8, products: '4 products', active: true, expanded: false },
            { name: 'Women’s Shoes', count: 7, products: '6 products', active: false, expanded: false },
          ],
        },
        {
          name: 'Health and Beauty',
          count: 10,
          products: '5 products',
          active: true,
          expanded: false,
          subcategories: [
            { name: 'Skincare', count: 5, products: '3 products', active: true, expanded: false },
            { name: 'Makeup', count: 5, products: '2 products', active: false, expanded: false },
          ],
        },
        {
          name: 'Electronics',
          count: 20,
          products: '12 products',
          active: true,
          expanded: false,
          subcategories: [
            { name: 'Laptops', count: 8, products: '5 products', active: true, expanded: false },
            { name: 'Smartphones', count: 12, products: '7 products', active: true, expanded: false },
          ],
        },
        {
          name: 'Furniture',
          count: 9,
          products: '6 products',
          active: false,
          expanded: false,
          subcategories: [
            { name: 'Living Room', count: 4, products: '2 products', active: true, expanded: false },
            { name: 'Bedroom', count: 5, products: '4 products', active: false, expanded: false },
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
/* Styling for the category and subcategory rows */
.category-row,
.subcategory-row,
.nested-subcategory-row {
  display: flex;
  justify-content: space-between;
  padding: 10px;
  border-bottom: 1px solid #ddd;
  cursor: pointer;
}

.category-row:hover,
.subcategory-row:hover,
.nested-subcategory-row:hover {
  background-color: #f9f9f9;
}

.category-status.active {
  color: green;
}

.category-status.inactive {
  color: red;
}

.edit-btn {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 5px 10px;
  cursor: pointer;
}

.edit-btn:hover {
  background-color: #0056b3;
}

ul {
  list-style-type: none;
  padding-left: 0;
}

.subcategory-list {
  padding-left: 20px;
}

.nested-subcategory-list {
  padding-left: 40px;
}

li {
  margin: 5px 0;
}
</style>
