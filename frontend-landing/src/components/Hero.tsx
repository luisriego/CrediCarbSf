// src/components/Hero.tsx
import React from 'react';
import styles from './Hero.module.scss'; // Import styles

const Hero: React.FC = () => {
  return (
    <section className={styles.heroSection}> {/* Apply style */}
      {/* This section can be developed further or merged if Header handles all hero content */}
    </section>
  );
};
export default Hero;
