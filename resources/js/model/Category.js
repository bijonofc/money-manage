class Category {
  constructor() {
    this.id = null;
    this.parent_id = null; // Always null, never ''
    this.name = '';
    this.type = 'expense';
    this.icon = 'tag';
    this.color = '#6366f1';
    this.is_system = false;
    this.is_active = true;
  }
}

export default Category;
