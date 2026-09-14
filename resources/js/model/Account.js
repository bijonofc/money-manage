class Account {
  constructor() {
    this.id = null;
    this.name = '';
    this.account_type = 'bank';
    this.account_number = '';
    this.balance = 0;
    this.currency = 'BDT';
    this.description = '';
    this.is_active = true;
    this.meta = null;
  }
}

export default Account;
