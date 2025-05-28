// src/components/Footer.tsx
import React from 'react';
import styles from './Footer.module.scss'; // Import styles

const Footer: React.FC = () => {
  return (
    <footer className={styles.siteFooter}> {/* Apply style */}
      <div className="container"> {/* Using global .container */}
        <p>&copy; 2024 CrediCarbSf. All rights reserved.</p>
      </div>
    </footer>
  );
};
export default Footer;
