import {
  // Food & Drinks
  Utensils,
  Coffee,
  CupSoda,
  Beer,
  Wine,
  Pizza,
  Soup,
  Sandwich,
  IceCream,
  CookingPot,
  Apple,
  Cake,

  // Shopping & Lifestyle
  ShoppingCart,
  ShoppingBag,
  Shirt,
  Watch,
  Glasses,
  Package,
  Store,
  Tag,

  // Income & Work
  Wallet,
  Banknote,
  Briefcase,
  Building2,
  Laptop,
  HandCoins,
  BadgeDollarSign,
  TrendingUp,

  // Finance & Banking
  Landmark,
  CreditCard,
  Coins,
  PiggyBank,
  Receipt,
  Percent,
  Scale,
  ChartPie,

  // Housing & Utilities
  Home,
  Zap,
  Droplets,
  Flame,
  Wifi,
  Lightbulb,
  Wrench,

  // Transportation & Travel
  Car,
  Fuel,
  Bus,
  Train,
  Plane,
  Bike,
  Ship,
  Truck,

  // Health, Fitness & Care
  Heart,
  Pill,
  Stethoscope,
  Dumbbell,
  Activity,
  Smile,
  Scissors,

  // Education, Family & Pets
  BookOpen,
  GraduationCap,
  Baby,
  PawPrint,
  Dog,
  FileText,

  // Entertainment & Hobbies
  Film,
  Tv,
  Music,
  Headphones,
  Gamepad2,
  Camera,
  Ticket,
  PartyPopper,

  // Personal & General
  Gift,
  Shield,
  Smartphone,
  Globe,
  MoreHorizontal,
  User,
  PieChart,
} from '@lucide/vue';

export const CATEGORY_ICON_LIST = [
  // --- Food & Beverages (12 icons) ---
  { id: 'utensils', name: 'Dining / Restaurant', component: Utensils, category: 'Food & Drinks', keywords: ['food', 'dining', 'restaurant', 'meal', 'lunch', 'dinner', 'eat', 'hotel'] },
  { id: 'coffee', name: 'Coffee & Cafe', component: Coffee, category: 'Food & Drinks', keywords: ['coffee', 'tea', 'cafe', 'espresso', 'snack', 'drink', 'beverage'] },
  { id: 'cup-soda', name: 'Drinks & Beverages', component: CupSoda, category: 'Food & Drinks', keywords: ['soda', 'drink', 'beverage', 'juice', 'cola', 'cold drink', 'cocktail', 'mocktail'] },
  { id: 'beer', name: 'Beer & Alcohol', component: Beer, category: 'Food & Drinks', keywords: ['beer', 'bar', 'pub', 'alcohol', 'brewery'] },
  { id: 'wine', name: 'Wine & Nightlife', component: Wine, category: 'Food & Drinks', keywords: ['wine', 'champagne', 'party', 'nightlife', 'bar', 'club'] },
  { id: 'pizza', name: 'Fast Food & Pizza', component: Pizza, category: 'Food & Drinks', keywords: ['pizza', 'fast food', 'burger', 'takeaway', 'takeout', 'junk'] },
  { id: 'soup', name: 'Soup & Stews', component: Soup, category: 'Food & Drinks', keywords: ['soup', 'stew', 'bowl', 'hot food', 'ramen', 'noodles'] },
  { id: 'sandwich', name: 'Snacks & Bakery', component: Sandwich, category: 'Food & Drinks', keywords: ['sandwich', 'bakery', 'bread', 'breakfast', 'toast', 'burger'] },
  { id: 'ice-cream', name: 'Dessert & Sweets', component: IceCream, category: 'Food & Drinks', keywords: ['ice cream', 'dessert', 'sweet', 'gelato', 'frozen'] },
  { id: 'cooking-pot', name: 'Cooking & Kitchen', component: CookingPot, category: 'Food & Drinks', keywords: ['cooking', 'kitchen', 'home cooked', 'pot', 'recipe'] },
  { id: 'apple', name: 'Fruits & Healthy', component: Apple, category: 'Food & Drinks', keywords: ['apple', 'fruit', 'vegetables', 'organic', 'diet', 'healthy'] },
  { id: 'cake', name: 'Cake & Birthday', component: Cake, category: 'Food & Drinks', keywords: ['cake', 'birthday', 'pastry', 'celebration', 'party'] },

  // --- Shopping & Lifestyle (8 icons) ---
  { id: 'shopping-cart', name: 'Groceries & Market', component: ShoppingCart, category: 'Shopping', keywords: ['cart', 'groceries', 'supermarket', 'market', 'bazar', 'grocery'] },
  { id: 'shopping-bag', name: 'Shopping & Retail', component: ShoppingBag, category: 'Shopping', keywords: ['shopping', 'bag', 'clothes', 'fashion', 'store', 'mall'] },
  { id: 'shirt', name: 'Clothing & Apparel', component: Shirt, category: 'Shopping', keywords: ['shirt', 'clothes', 'apparel', 'fashion', 'outfit', 'wear'] },
  { id: 'watch', name: 'Luxury & Jewelry', component: Watch, category: 'Shopping', keywords: ['watch', 'luxury', 'jewelry', 'accessories', 'gold', 'clock'] },
  { id: 'glasses', name: 'Eyewear & Optics', component: Glasses, category: 'Shopping', keywords: ['glasses', 'spectacles', 'sunglasses', 'optics', 'lenses'] },
  { id: 'package', name: 'Online Delivery', component: Package, category: 'Shopping', keywords: ['package', 'parcel', 'courier', 'delivery', 'order', 'amazon', 'aliexpress'] },
  { id: 'store', name: 'Local Store', component: Store, category: 'Shopping', keywords: ['store', 'shop', 'vendor', 'retail', 'market', 'local'] },
  { id: 'tag', name: 'General / Sales', component: Tag, category: 'Shopping', keywords: ['tag', 'discount', 'sale', 'label', 'general', 'promo'] },

  // --- Income & Earnings (8 icons) ---
  { id: 'wallet', name: 'Salary & Wages', component: Wallet, category: 'Income', keywords: ['salary', 'wages', 'wallet', 'payroll', 'paycheck', 'earnings'] },
  { id: 'banknote', name: 'Cash Income', component: Banknote, category: 'Income', keywords: ['cash', 'notes', 'money', 'income', 'currency'] },
  { id: 'briefcase', name: 'Business / Career', component: Briefcase, category: 'Income', keywords: ['job', 'work', 'office', 'profession', 'career', 'corporate'] },
  { id: 'building-2', name: 'Enterprise / Company', component: Building2, category: 'Income', keywords: ['enterprise', 'shop', 'business', 'company', 'building'] },
  { id: 'laptop', name: 'Freelance & Tech', component: Laptop, category: 'Income', keywords: ['freelance', 'tech', 'software', 'laptop', 'client', 'remote', 'consulting'] },
  { id: 'hand-coins', name: 'Tips & Bonuses', component: HandCoins, category: 'Income', keywords: ['tips', 'bonus', 'commission', 'gratuity', 'incentive'] },
  { id: 'badge-dollar-sign', name: 'Awards & Royalties', component: BadgeDollarSign, category: 'Income', keywords: ['award', 'royalty', 'prize', 'scholarship', 'affiliate'] },
  { id: 'trending-up', name: 'Investments', component: TrendingUp, category: 'Income', keywords: ['investment', 'stocks', 'profit', 'shares', 'dividend', 'growth', 'crypto'] },

  // --- Finance & Banking (8 icons) ---
  { id: 'landmark', name: 'Bank & Institutional', component: Landmark, category: 'Finance', keywords: ['bank', 'deposit', 'financial', 'institution', 'wire', 'transfer'] },
  { id: 'credit-card', name: 'Cards, EMI & Debt', component: CreditCard, category: 'Finance', keywords: ['credit-card', 'card', 'debt', 'loan', 'emi', 'repayment', 'visa'] },
  { id: 'coins', name: 'Savings & Coins', component: Coins, category: 'Finance', keywords: ['coins', 'cash', 'savings', 'plus-circle', 'interest', 'reserve'] },
  { id: 'piggy-bank', name: 'Savings Goals', component: PiggyBank, category: 'Finance', keywords: ['piggy', 'savings', 'goal', 'deposit', 'emergency fund'] },
  { id: 'receipt', name: 'Bills & Invoices', component: Receipt, category: 'Finance', keywords: ['receipt', 'bill', 'invoice', 'statement', 'check'] },
  { id: 'percent', name: 'Interest & Taxes', component: Percent, category: 'Finance', keywords: ['percent', 'interest', 'tax', 'vat', 'discount', 'gst'] },
  { id: 'scale', name: 'Legal & Fines', component: Scale, category: 'Finance', keywords: ['legal', 'fine', 'lawyer', 'court', 'penalty', 'justice'] },
  { id: 'chart-pie', name: 'Portfolio & Assets', component: ChartPie, category: 'Finance', keywords: ['portfolio', 'assets', 'mutual funds', 'allocation', 'wealth'] },

  // --- Housing & Utilities (7 icons) ---
  { id: 'home', name: 'Housing & Rent', component: Home, category: 'Housing', keywords: ['home', 'house', 'rent', 'housing', 'mortgage', 'flat', 'apartment'] },
  { id: 'zap', name: 'Electricity & Power', component: Zap, category: 'Housing', keywords: ['zap', 'electricity', 'power', 'bills', 'utilities', 'energy'] },
  { id: 'droplets', name: 'Water & Gas', component: Droplets, category: 'Housing', keywords: ['water', 'droplets', 'gas', 'sewer', 'utilities'] },
  { id: 'flame', name: 'Gas & Heating', component: Flame, category: 'Housing', keywords: ['gas', 'flame', 'heating', 'cylinder', 'lpg'] },
  { id: 'wifi', name: 'Internet & Telecom', component: Wifi, category: 'Housing', keywords: ['wifi', 'internet', 'broadband', 'data', 'fiber'] },
  { id: 'lightbulb', name: 'Home Supplies', component: Lightbulb, category: 'Housing', keywords: ['lightbulb', 'idea', 'lamp', 'decor', 'household'] },
  { id: 'wrench', name: 'Repairs & Plumbing', component: Wrench, category: 'Housing', keywords: ['repair', 'maintenance', 'plumber', 'tools', 'handyman'] },

  // --- Transportation & Travel (8 icons) ---
  { id: 'car', name: 'Car & Auto', component: Car, category: 'Transport', keywords: ['car', 'transport', 'auto', 'ride', 'taxi', 'uber', 'drive'] },
  { id: 'fuel', name: 'Fuel & Gas Station', component: Fuel, category: 'Transport', keywords: ['fuel', 'gas', 'petrol', 'diesel', 'cng', 'charging'] },
  { id: 'bus', name: 'Bus & Public Transit', component: Bus, category: 'Transport', keywords: ['bus', 'coach', 'transit', 'commute'] },
  { id: 'train', name: 'Train & Metro', component: Train, category: 'Transport', keywords: ['train', 'metro', 'railway', 'subway'] },
  { id: 'plane', name: 'Travel & Flights', component: Plane, category: 'Transport', keywords: ['plane', 'travel', 'flight', 'tour', 'holiday', 'vacation', 'air'] },
  { id: 'bike', name: 'Bicycle & Bike', component: Bike, category: 'Transport', keywords: ['bike', 'bicycle', 'motorcycle', 'scooter', 'ride'] },
  { id: 'ship', name: 'Ferry & Cruise', component: Ship, category: 'Transport', keywords: ['ship', 'boat', 'ferry', 'cruise', 'water transport'] },
  { id: 'truck', name: 'Moving & Cargo', component: Truck, category: 'Transport', keywords: ['truck', 'moving', 'freight', 'cargo', 'transport'] },

  // --- Health, Fitness & Care (7 icons) ---
  { id: 'heart', name: 'Healthcare & Clinic', component: Heart, category: 'Health', keywords: ['health', 'medical', 'medicine', 'doctor', 'hospital', 'clinic'] },
  { id: 'pill', name: 'Pharmacy & Meds', component: Pill, category: 'Health', keywords: ['pill', 'pharmacy', 'medicine', 'drugs', 'prescription'] },
  { id: 'stethoscope', name: 'Doctor & Checkup', component: Stethoscope, category: 'Health', keywords: ['stethoscope', 'doctor', 'checkup', 'hospital', 'consultation'] },
  { id: 'dumbbell', name: 'Gym & Fitness', component: Dumbbell, category: 'Health', keywords: ['gym', 'fitness', 'workout', 'weights', 'crossfit', 'exercise'] },
  { id: 'activity', name: 'Sports & Active', component: Activity, category: 'Health', keywords: ['fitness', 'sports', 'activity', 'running', 'cardio'] },
  { id: 'smile', name: 'Personal Care', component: Smile, category: 'Health', keywords: ['smile', 'care', 'spa', 'beauty', 'wellness', 'hygiene'] },
  { id: 'scissors', name: 'Salon & Haircut', component: Scissors, category: 'Health', keywords: ['salon', 'haircut', 'barber', 'shave', 'hair'] },

  // --- Education, Family & Pets (6 icons) ---
  { id: 'book-open', name: 'Education & Books', component: BookOpen, category: 'Education', keywords: ['book', 'education', 'study', 'books', 'school', 'tuition', 'read'] },
  { id: 'graduation-cap', name: 'Courses & Degree', component: GraduationCap, category: 'Education', keywords: ['course', 'university', 'college', 'degree', 'training', 'learning'] },
  { id: 'baby', name: 'Kids & Baby Care', component: Baby, category: 'Family', keywords: ['baby', 'kids', 'child', 'children', 'toys', 'daycare'] },
  { id: 'paw-print', name: 'Pet Care & Vet', component: PawPrint, category: 'Family', keywords: ['pet', 'vet', 'animal', 'veterinary', 'paws'] },
  { id: 'dog', name: 'Dogs & Pets', component: Dog, category: 'Family', keywords: ['dog', 'cat', 'puppy', 'pet food', 'pets'] },
  { id: 'file-text', name: 'Docs & Subscriptions', component: FileText, category: 'General', keywords: ['file', 'docs', 'subscription', 'membership', 'paperwork'] },

  // --- Entertainment & Hobbies (8 icons) ---
  { id: 'film', name: 'Movies & Cinema', component: Film, category: 'Entertainment', keywords: ['entertainment', 'film', 'movie', 'cinema', 'theatre', 'show'] },
  { id: 'tv', name: 'TV & Streaming', component: Tv, category: 'Entertainment', keywords: ['tv', 'television', 'streaming', 'netflix', 'youtube'] },
  { id: 'music', name: 'Music & Concerts', component: Music, category: 'Entertainment', keywords: ['music', 'songs', 'spotify', 'concert', 'audio'] },
  { id: 'headphones', name: 'Podcasts & Audio', component: Headphones, category: 'Entertainment', keywords: ['headphones', 'podcast', 'audiobook', 'sound'] },
  { id: 'gamepad-2', name: 'Video Games', component: Gamepad2, category: 'Entertainment', keywords: ['gaming', 'games', 'game', 'playstation', 'steam', 'xbox'] },
  { id: 'camera', name: 'Photography', component: Camera, category: 'Entertainment', keywords: ['camera', 'photo', 'video', 'photography', 'shoot'] },
  { id: 'ticket', name: 'Events & Tickets', component: Ticket, category: 'Entertainment', keywords: ['ticket', 'event', 'show', 'amusement', 'entry'] },
  { id: 'party-popper', name: 'Parties & Events', component: PartyPopper, category: 'Entertainment', keywords: ['party', 'celebration', 'festival', 'holiday', 'events'] },

  // --- Personal & General (5 icons) ---
  { id: 'gift', name: 'Gifts & Charity', component: Gift, category: 'Personal', keywords: ['gift', 'presents', 'charity', 'donation', 'bonus', 'refund'] },
  { id: 'shield', name: 'Insurance & Safety', component: Shield, category: 'Personal', keywords: ['insurance', 'shield', 'safety', 'policy', 'protection'] },
  { id: 'smartphone', name: 'Mobile & Recharge', component: Smartphone, category: 'Personal', keywords: ['mobile', 'phone', 'recharge', 'cellular', 'telecom'] },
  { id: 'globe', name: 'Foreign & Online', component: Globe, category: 'Personal', keywords: ['globe', 'international', 'foreign', 'worldwide', 'currency exchange'] },
  { id: 'more-horizontal', name: 'Other Expenses', component: MoreHorizontal, category: 'General', keywords: ['other', 'misc', 'more-horizontal', 'extra'] },
];

const ICON_MAP = {};
CATEGORY_ICON_LIST.forEach((item) => {
  ICON_MAP[item.id] = item.component;
});

// Backward-compatibility aliases with seeds and legacy identifiers
ICON_MAP['building'] = Building2;
ICON_MAP['bag'] = ShoppingBag;
ICON_MAP['book'] = BookOpen;
ICON_MAP['plus-circle'] = Coins;
ICON_MAP['more'] = MoreHorizontal;
ICON_MAP['drinks'] = CupSoda;
ICON_MAP['beverage'] = CupSoda;
ICON_MAP['user'] = User;
ICON_MAP['person'] = User;
ICON_MAP['pie-chart'] = PieChart || ChartPie;
ICON_MAP['piechart'] = PieChart || ChartPie;
ICON_MAP['chart-pie'] = ChartPie;
ICON_MAP['chartpie'] = ChartPie;
ICON_MAP['tag'] = Tag;

/**
 * Resolve an icon component from an icon identifier, or guess from category title
 * @param {string} iconNameOrTitle 
 * @returns {Component} Lucide Vue icon component
 */
export function resolveCategoryIcon(iconNameOrTitle) {
  if (!iconNameOrTitle) return Tag;

  const raw = String(iconNameOrTitle).trim();
  const lower = raw.toLowerCase();

  // 1. Direct match in icon map
  if (ICON_MAP[lower]) {
    return ICON_MAP[lower];
  }

  // 2. Normalized kebab-case match (handles camelCase, underscores, spaces, lucide: prefix)
  const normalized = lower
    .replace(/([a-z0-9])([A-Z])/g, '$1-$2')
    .replace(/[_\s]+/g, '-')
    .replace(/^(i-)?lucide[-:]/, '');

  if (ICON_MAP[normalized]) {
    return ICON_MAP[normalized];
  }

  // 3. Exact match on item id
  const directItem = CATEGORY_ICON_LIST.find((i) => i.id === lower || i.id === normalized);
  if (directItem) {
    return directItem.component;
  }

  // 4. Keyword / title search fallback
  for (const item of CATEGORY_ICON_LIST) {
    if (item.keywords.some((kw) => lower.includes(kw) || normalized.includes(kw))) {
      return item.component;
    }
  }

  return Tag;
}
